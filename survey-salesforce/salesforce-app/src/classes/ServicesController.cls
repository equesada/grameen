
public with sharing class ServicesController {

    public String Form { get; set; }
    public String msg { get; set; }
    public final String ERR_MANY_DOC='More than one document for this survey';
    public final String ERR_NO_SURVEY = 'There is no survey to export';
    public final String ERR_EMPTY_DOC='Document didn\'t contain any content';
    public final String ERR_EMPTY_XML='Submission XML was\'t found in the header';
    public final String ERR_INVALID_SURVEY_XML = 'This Survey is no longer available to receive answers';
    public Survey__c currentSurvey;
    /*Response messages for login attemps*/

    public final static String CORRECT_LOGIN = 'OK';
    public final static String INVALID_DEVICE_LOGIN = 'Invalid device';
    public final static String INVALID_AUTHENTICATION_LOGIN  = 'Invalid Username or Password';

    /*Login parameters names*/

    public final static String LOGIN_USERNAME_PARAM = 'user';
    public final static String LOGIN_PASSWORD_PARAM = 'pass';
    public final static String LOGIN_IMEI_PARAM = 'imei';

    public static final String SURVEY_ANSWER_ATTACHMENT_NAME = 'submission.xml';

    public PageReference process() {
        XFormCreator x = new XFormCreator();
        Id surveyId = ApexPages.currentPage().getParameters().get('id');
        String user = ApexPages.currentPage().getHeaders().get(LOGIN_USERNAME_PARAM);
        String pass = ApexPages.currentPage().getHeaders().get(LOGIN_PASSWORD_PARAM);
        String imei = ApexPages.currentPage().getHeaders().get(LOGIN_IMEI_PARAM);
        LoginResult loginResult = this.Login(user, pass, imei);
        if(loginResult.result == CORRECT_LOGIN){
            if (surveyId==null){
                // When working on a Salesforce Site, we will have to
                // remove the "/apex" prefix from the relative Url
                Integer prefLenght = Site.getPrefix() == null ? 0 : 5;
                // Generate the base URL for survey fetching
                string host = ApexPages.currentPage().getHeaders().get('Host');
                string path = Page.formList.getUrl().substring(prefLenght);
                string fullUrl = 'http://' + host + path;
                // Actual work: generate the list of surveys
                Apexpages.currentPage().getHeaders().put('X-OpenRosa-Version', '1.0');
                Form = x.getXmlAllSurveys(fullUrl,loginresult.contactId).toXmlString();
            }else{
                this.getSurvey(surveyId);
            }
        }else {
            this.Form = '<error>' + loginResult.result + '</error>';

        }
        return ApexPages.currentPage();
    }
        // Action that is called to create the Document with the xForms exported

    public void getSurvey(Id surveyId) {
        XFormCreator x = new XFormCreator();
        Survey__c[] surveys = [Select v.Name,v.Alias__c, v.Id,v.InterviewMapField__c,v.Close_Message__c,v.CreatedDate,v.Gps_Location_Enabled__c,Gps_ShowMap__c From Survey__c v where Id=:surveyId AND Status__c='Published'];
        msg = x.Message;
        if(surveys.size()>0){
            Attachment[] attachments = [SELECT Id,Body FROM Attachment where ParentId = :surveyId and Name = :XFormCreator.XFORM_ATTACHMENT];
            if (attachments.size()==0 || attachments.size()!=1) {
                x.createDocument(surveys[0]);
                Form = [SELECT Id,Body FROM Attachment where ParentId = :surveyId and Name = :XFormCreator.XFORM_ATTACHMENT].body.toString();
            }else{
                Form = attachments[0].body.toString();
                if (Form == null || Form == '')
                    msg = ERR_EMPTY_DOC;

            }
        }else {
            msg = ERR_NO_SURVEY;

        }
    }

    //method to parse Submission, and all his
    //associated Answer,returns the Submission Id
    public Id parseSubmission(String xml,String Last,String surveyorId,String deviceId)
    {
        //MD5 Hash to indentify the Submission so we can avoid duplicates values.
        String MD5Hash = EncodingUtil.base64Encode(Crypto.generateDigest('MD5',blob.valueof(xml)));
        Answer__c qa = new Answer__c();
        String submissionId=null;
        boolean endOfxml = false;
        String surveyName = '';
        boolean surveyFound = false;
        Xmlstreamreader xmlReader = new Xmlstreamreader(xml);
        Id surveyId ;
        List<Question__c> questions;

        //loop to find the Survey name and id
        while(xmlReader.hasNext() && !(surveyFound)) {

            if (xmlReader.getEventType() == XmlTag.START_ELEMENT){
                surveyName = xmlReader.getLocalName();
                surveyId = xmlReader.getAttributeValue(null, 'id');
                surveyFound= true;
            }

            xmlReader.next();
        }
        List<Survey__c> result = [select Id,InterviewMapField__c,Close_Message__c,Name,IsPPI__c from Survey__c where id = :surveyId AND Status__c IN ('Published', 'Deprecated')];

        //if the survey exist and with a correct status,proced with
        //the header parsing to save the Submission
        if (result.size()>0) {
            surveyId= result[0].Id;
			currentSurvey = result[0];
            //Checks if the submission already exists
            Submission__c[] existentSubmission= [SELECT Id FROM Submission__c WHERE Survey__c=:surveyId and SubmissionXMLMD5__c=:MD5Hash];
            //If found any duplicate submission returns the Id of the already existing submission.
            if(existentSubmission.size() > 0)
                return existentSubmission[0].Id;
            questions = [SELECT Id, Name, Type__c FROM Question__c WHERE Survey__c = :surveyId ];
            Map<String,Question__c> questionNameToId = new Map<string,Question__c>();
            for( Question__c question : questions){
                questionNameToId.put(question.Name,question);
            }

            Map<Id,Option__c> optionsMap = new Map<Id,Option__c>([
                SELECT Id,Name,Caption__c
                FROM Option__c
                WHERE Question__r.Survey__c = :surveyId
                ORDER BY Question__r.Position__c, Position__c]);


            /*
                        Parse the Header of the Xml and check
                        IF THE Last parameter is in true, we mark the submission as uploaded
            */
            Submission__c su = this.submissionHeaderParse(xmlReader);
            su.Status__c =(Last == 'true')? 'Uploaded' :'Uploading';
            su.Survey__c = surveyId;
            su.Surveyor__c = SurveyorId;
            su.Device__c = DeviceId;
            su.SubmissionXMLMD5__c = MD5Hash;
            insert su;
            submissionId = su.Id;
               List<Answer__c> answers = new List<Answer__c>();

               //loop to search all the question Answer in the xml
               while(xmlReader.hasNext() && !(endOfxml)) {
                 if (xmlReader.getEventType() == XmlTag.END_ELEMENT) {
                     if (xmlReader.getLocalName()==surveyName) {
                         endOfXml = true;
                     }
                 xmlReader.next();
                 continue;
                } else if (xmlReader.getEventType() == XmlTag.START_ELEMENT) {
                    //Save the Questions Answer of the Survey Answer in the header
                    Question__c question = questionNameToId.get(xmlReader.getLocalName());
                    if(question!=null && question.Type__c!= QuestionType.QUESTION_SECTION){
                        if (question.Type__c==QuestionType.QUESTION_CHECKBOX || question.Type__c==QuestionType.QUESTION_RADIO){
                            xmlReader.next();
                            this.ParseCheckboxQuestion(xmlReader, question, su, optionsMap, answers);
                            xmlReader.next();
                        }else {
                             this.ParseSimpleQuestion(xmlReader, question, su, answers);
                        }
                    }

                 }
                 xmlReader.next();
            }

            insert answers;
            MapQuestionAnswer(answers,surveyId,submissionId);
            calculateScores(answers,surveyId,submissionId);
            if(result[0].IsPPI__c)
                calculatePPIScores(answers,surveyId,submissionId);
        }

        return submissionId;
    }
    /*
     *Calculates the total survey's PPI score and Creates the SubmissionScore record.
     */
    private void calculatePPIScores(List<Answer__c> answers, string surveyId,string submissionId){
        answers = [SELECT Id,Option__r.PPIScore__c,Option__c FROM Answer__c Where Id in: answers];
        //Sum all the PPI Scoring of each answer.
        Decimal ppiScoringSUM = 0;
        for(Answer__c ans:answers){
        if(ans.Option__c != null && ans.Option__r.PPIScore__c != null)
            ppiScoringSUM += ans.Option__r.PPIScore__c;
        }
        Submission__c submission = new Submission__c(id=submissionId,PPIScore__c = ppiScoringSUM);
        update submission;
        PPITableLine__c[] PPITableLines = [SELECT Id,From__c,Probability__c,To__c,PPITableDataSet__c FROM PPITableLine__c Where PPITableDataSet__r.Survey__c = :surveyId];
        Map<String,List<PPITableLine__c>> ppiTableLinesByDataSet = new Map<String,List<PPITableLine__c>>();
        for(PPITableLine__c ppiTL:PPITableLines){
            List<PPITableLine__c> tableLines = ppiTableLinesByDataSet.get(ppiTL.PPITableDataSet__c);
            if(tableLines == null){
                tableLines = new List<PPITableLine__c>{ppiTL};
                ppiTableLinesByDataSet.put(ppiTL.PPITableDataSet__c,tableLines);
            }else{
                tableLines.add(ppiTL);
            }
        }
        SubmissionPPIValue__c[] subVals = new SubmissionPPIValue__c[]{};
        for(String ppiDataSetId:ppiTableLinesByDataSet.keySet()){
            List<PPITableLine__c> ppiLines = ppiTableLinesByDataSet.get(ppiDataSetId);
            for(PPITableLine__c pLine:ppiLines){
                if(pLine.From__c <= ppiScoringSUM && pLine.To__c >= ppiScoringSUM){
                    subVals.add(new SubmissionPPIValue__c(PPITableLine__c = pLine.Id,Submission__c = submissionId));
                    break;
                }
            }
        }
        insert subVals;
    }

    /*
     *Calculates the total survey's score and Creates the SubmissionScore record.
     */
    private void calculateScores(List<Answer__c> answers, string surveyId,string submissionId){
        // 1. Retrieve scoring groups for survey
        List<ScoringGroup__c> scoringGroups = [SELECT Id FROM ScoringGroup__c WHERE Survey__c =:surveyId];
        List<ScoreValue__c> scoreValues = [SELECT Id,Value__c,Option__c,ScoringGroup__c FROM ScoreValue__c WHERE ScoringGroup__r.Survey__c =:surveyId];

        // 2. Retrieve option's values for survey
        Map <Id,Map<Id,Decimal>> optionIdToScores = new Map<Id,Map<Id,Decimal>>();
        if(scoringGroups.size() != 0 && scoreValues.size() != 0) {
            //Stores each possible score value for one option in each ScoringGroup.
            //Final structure is OptionId -> ( ScoringGroupId -> Value )
            for( ScoreValue__c score : scoreValues){
                Map<Id,Decimal> Values = optionIdToScores.get(score.Option__c);
                if(Values == null)
                    optionIdToScores.put(score.Option__c,new Map<Id,Decimal>{score.ScoringGroup__c=>score.Value__c});
                else {
                    Values.put(score.ScoringGroup__c,score.Value__c);
                }
            }
        }

          // 3. For each option, get it's value for each question group and add it to that group's total
        Map<Id,SubmissionScore__c> totalScorePerScoringGroup = new Map<Id,SubmissionScore__c>();
        for(Answer__c qAns:answers){
            Map<Id,Decimal> optionScoreValues= optionIdToScores.get(qAns.Option__c);
            if (optionScoreValues != null){
                for (Id sGroup:optionScoreValues.keySet()) {
                    SubmissionScore__c sAS = totalScorePerScoringGroup.get(sGroup);
                    if (sAS == null){
                        Decimal totalScore = (optionScoreValues.get(sGroup) == null)?0:optionScoreValues.get(sGroup);
                        totalScorePerScoringGroup.put(sGroup,new SubmissionScore__c(ScoringGroup__c = sGroup,TotalScore__c = totalScore,Submission__c = submissionId));
                    }else {
                        sAS.TotalScore__c += (optionScoreValues.get(sGroup) == null)?0:optionScoreValues.get(sGroup);
                    }
                }
            }
        }

        // 4. Insert each SubmissionScore object
        if(totalScorePerScoringGroup.values().size() != 0)
            insert (totalScorePerScoringGroup.values());
    }
    /* Method to Parse the Header of the Survey Answer*/
    private Submission__c submissionHeaderParse(Xmlstreamreader xmlReader){
        Submission__c su = new Submission__c();
        boolean endOfHeader = false;
        String IntervieweeId ='';
        while(xmlReader.hasNext() && !(endOfHeader)) {

                if (xmlReader.getEventType() == XmlTag.END_ELEMENT) {
                    xmlReader.next();
                    continue;
                }

                else if (xmlReader.getEventType() == XmlTag.START_ELEMENT){
                    if (xmlReader.getLocalName()=='today'){
                        xmlReader.next();
                        if (xmlReader.getEventType() == XmlTag.CHARACTERS){
                            su.Date__c = Date.valueOf(xmlReader.getText());
                        }
                    }

                    else if (xmlReader.getLocalName()=='start'){
                        xmlReader.next();
                        if (xmlReader.getEventType() == XmlTag.CHARACTERS){
                            String startDateTime = xmlReader.getText().replace('T', ' ');
                            su.startDate__c = dateTime.valueOf(startDateTime);
                        }
                    }
                    else if (xmlReader.getLocalName()=='end'){
                        xmlReader.next();
                        if (xmlReader.getEventType() == XmlTag.CHARACTERS){
                            String endDateTime = xmlReader.getText().replace('T', ' ');
                            su.endDate__c = dateTime.valueOf(endDateTime);
                        }
                    }
                    else if (xmlReader.getLocalName()=='deviceid'){
                        //WE ARE NOT CURRENTLY SAVING THE deviceid
                    }
                    else if (xmlReader.getLocalName()=='subscriberid'){
                        xmlReader.next();
                        IntervieweeId = xmlReader.getText();


                    }
                    else if (xmlReader.getLocalName()=='simid'){
                        //WE ARE NOT CURRENTLY SAVING THE simid
                    } else if (xmlReader.getLocalName()=='location'){
                         //Parse the location in the survey Answer
                         xmlReader.next();
                         if(xmlReader.getEventType() == XmlTag.CHARACTERS){
                             parseLocation(xmlReader.getText(),su);
                         }
                    }
                    else if (xmlReader.getLocalName()=='phonenumber'){
                        endOfHeader = true;
                    }

                }
                xmlReader.next();
            }

        /*Check if an intervieweeId was sent in the answer,if it's a valid Id for a
            contact save the interviewee in the Submission,
            otherwise the submission is saved but no interviewee associated*/
        if (IntervieweeId != '' && currentSurvey.InterviewMapField__c != null){
            List <SObject> cont = Database.query('SELECT Id FROM Contact WHERE ' + currentSurvey.InterviewMapField__c+ '=\'' + intervieweeId + '\'');
            if (cont.size() == 1){
                su.Interviewee__c = cont[0].Id;
            }else su.Interviewee__c = null;
        }
        return su;
    }
    /* Method to Parse the checkbox Question type of the Survey Answer*/
    private void parseCheckboxQuestion(Xmlstreamreader xmlReader,Question__c question,Submission__c su,Map<Id,Option__c> optionsMap,List<Answer__c> answers){
        if (xmlReader.getEventType() == XmlTag.CHARACTERS){
            List<String> checkedAnswers = xmlReader.getText().split(' ', 0);
            for (String s:checkedAnswers){
                Answer__c qa = new Answer__c(
                Submission__c = su.Id,
                Question__c = question.Id,
                Option__c = s,
                TextValue__c = optionsMap.get(s).Caption__c,
                Value__c=optionsMap.get(s).Caption__c);
                answers.add(qa);
            }
        }
    }

    /*Method to parse the text,number,date and radio question types*/
    private void parseSimpleQuestion(Xmlstreamreader xmlReader,Question__c question,Submission__c su,List<Answer__c> answers){

        // Get the answer for this question.
        xmlReader.next();
        String questionValue = '';
        while (xmlReader.getEventType() == XmlTag.CHARACTERS){
            questionValue = questionValue + xmlReader.getText();
            xmlReader.next();
        }
        questionValue = questionValue.trim();

        // If there's no answer, don't save an Answer__c object.
        if (questionValue == '')
            return;

        // Create the Answer__c object that will be inserted, and load appropriate field.
        Answer__c qa = new Answer__c(
            Submission__c = su.Id,
            Question__c = question.Id,
            Value__c = questionValue);

        if(question.Type__c == QuestionType.QUESTION_NUMBER){
            qa.NumericValue__c = decimal.valueOf(questionValue);

        }else if(question.Type__c == QuestionType.QUESTION_DATETIME || question.Type__c == QuestionType.QUESTION_DATE){
            qa.Value__c = questionValue.replace('T', ' ');
            qa.DateValue__c = date.valueOf(qa.Value__c);
            if(question.Type__c == QuestionType.QUESTION_DATETIME)
                qa.DateTimeValue__c = dateTime.valueOf(qa.Value__c);

        }else if(question.Type__c == QuestionType.QUESTION_RADIO || question.Type__c == QuestionType.QUESTION_TEXT_SHORT)
            qa.TextValue__c = String.valueOf(questionValue);

        answers.add(qa);
    }

    //METHOD THAT PROCESS THE SURVEY SUBMISSION

    public PageReference submit(){

        //Xml string obtained from a page param
        String xml = ApexPages.currentPage().getHeaders().get('surveyAnswer');
        String user = ApexPages.currentPage().getHeaders().get(LOGIN_USERNAME_PARAM);
        String pass = ApexPages.currentPage().getHeaders().get(LOGIN_PASSWORD_PARAM);
        String imei = ApexPages.currentPage().getHeaders().get(LOGIN_IMEI_PARAM);
        LoginResult loginResult = this.Login(user, pass, imei);
        //process the xml and get the Id of the survey answer

        if (loginResult.result == CORRECT_LOGIN ){
            if (xml!=null){
                String Last = ApexPages.currentPage().getParameters().get('Last');
                Id submissionId = this.ParseSubmission(xml,Last,loginResult.contactId,loginResult.deviceId);
                //if there is no Id,report error on submit
                if (submissionId!=null){
                    this.Form = '<ID>' + submissionId +  '</ID>';
                    String docName = submissionId + '.xml';
                    this.WriteDocument(submissionId, xml);
                } else {
                    this.Form = '<error>' + ERR_INVALID_SURVEY_XML +  '</error>';
                }
            }else{
                this.Form = '<error>' + ERR_EMPTY_XML +  '</error>';
            }
        }else {
            this.Form = '<error>' + loginResult.result +  '</error>';
        }
        return null;
    }


    /*Method used to log in, grant access to the services,and register the new devices
        */
    public LoginResult  login(String user,String pass,String imei){
        LoginResult result= new LoginResult();
        List<Contact> loggedContact;
        List<Device__c> usedDevice;
        if(user == null || pass == null){
            result.result=INVALID_AUTHENTICATION_LOGIN;
            return result;
        }else if(imei == null){
            result.result=INVALID_DEVICE_LOGIN;
            return result;
        }
        loggedContact = DBFullAccess.QuerySObject('Select Id,Name,mobilesurveys_LastCheckInDate__c,mobilesurveys_LastCheckInDevice__c from Contact where mobilesurveys_Username__c=\''+ String.escapeSingleQuotes(user) +'\' and ' + 'mobilesurveys_Password__c=\'' + String.escapeSingleQuotes(pass) + '\'');
        usedDevice = [select Id,Status__c,LastCheckInBy__c,LastCheckInDate__c from Device__c where IMEI__c=:imei];

        //Check that the combination password/id exist for a contact,else deny access

        if (loggedContact.size()==1){
            if (usedDevice.size()==1){

            //Check that the device is not in a 'Inactive' status,else deny access

                if (usedDevice[0].Status__c == 'Active'){
                    usedDevice[0].LastCheckInBy__c = loggedContact[0].Id;
                    usedDevice[0].LastCheckInDate__c = datetime.now();
                    //Since we will relate this with a Contact that could be private, we execute this as Super User.
                    DBFullAccess.UpdateSObject(usedDevice[0]);
                    result.result = CORRECT_LOGIN;
                    result.contactId = loggedContact[0].Id;
                    result.deviceId = usedDevice[0].id;
                    loggedContact[0].mobilesurveys_LastCheckInDevice__c =usedDevice[0].Id ;
                    loggedContact[0].mobilesurveys_LastCheckInDate__c =datetime.now();
                    DBFullAccess.UpdateSObject(loggedContact[0]);
                }else{
                    result.result=INVALID_DEVICE_LOGIN;
                }

            }else{

                //In case of a Correct Contact,and and unknow device,
                //this device is registred
                Device__c newDevice = new Device__c(imei__c=imei,
                                                LastCheckInBy__c = loggedContact[0].Id,
                                                    LastCheckInDate__c = datetime.now(),
                                                    Name = imei);
                DBFullAccess.InsertSObject(newDevice);
                result.result = CORRECT_LOGIN;
                result.contactId = loggedContact[0].Id;
                result.deviceId = newDevice.id;
                loggedContact[0].mobilesurveys_LastCheckInDevice__c =newDevice.Id ;
                loggedContact[0].mobilesurveys_LastCheckInDate__c =datetime.now();
                DBFullAccess.UpdateSObject(loggedContact[0]);
            }
        }else {
            result.result=INVALID_AUTHENTICATION_LOGIN;
        }
        return result;
    }

    /*Class used to set the result variables in a login attemp*/

    public class LoginResult{
          public String result;
          public Id contactId;
          public Id deviceId;
    }
    public void WriteDocument(Id submissionId, String content){
        insert new Attachment(
            Name = SURVEY_ANSWER_ATTACHMENT_NAME,
            ParentId = submissionId,
            Body = Blob.valueOf(content)
            );
    }

    /*on answer submission,save the data in the mapped objects if necessary*/

    public void MapQuestionAnswer(List<Answer__c> answers,String versionId,String submissionId){
        Map<Id,String> answersByQuestion= new Map<Id,String>();
        Set<Id> questionIds= new Set<Id>();
        for (Answer__c qa:answers){

            questionIds.add(String.valueof(qa.Question__c));
            //if aquestion has several answers(checkbox case)concatenate them in csv format
            if (answersByQuestion.get(qa.Question__c)==null){
                 answersByQuestion.put(qa.Question__c,qa.Value__c);
            }else{
                answersByQuestion.put(qa.Question__c,answersByQuestion.get(qa.Question__c) +';'+ qa.Value__c);
            }
        }
        List<QuestionMapping__c> questionMappingList=[select Id,FieldApiName__c,Question__c,SurveyMapping__c from QuestionMapping__c where Question__c in :questionIds];
        List<SurveyMapping__c> surveyMappingList=[select Id,Survey__c,ObjectApiName__c,MatchingField__c from SurveyMapping__c where Survey__c=:versionId];

        //iterate over the SurveyMappings objects to create/update the respective objects
        for (SurveyMapping__c surveyMapping:surveyMappingList){
            SObject mappedObject;
            Schema.DescribeSObjectResult mappedObjectDescribe = Schema.getGlobalDescribe().get(surveyMapping.ObjectApiName__c).getDescribe();
            Map<String, Schema.SObjectField> FieldDescribeMap= mappedObjectDescribe.fields.getMap();
            String surveyMappedFields='';
            Map<String,String> fieldValueMap = new Map<String,String>();
            String matchingData;
            String matchingField;
            Integer i=0;
            boolean referenceFieldFound = false;
            //get all the fields and the field values to insert/update in a Map <field,fieldValue>
            for(QuestionMapping__c questionMapping:questionMappingList){

                if(questionMapping.SurveyMapping__c == surveyMapping.Id){
                    surveyMappedFields = surveyMappedFields + questionMapping.FieldApiName__c + ';';
                    fieldValueMap.put(questionMapping.FieldApiName__c,answersByQuestion.get(questionMapping.Question__c));
                    //find the Matching field to get the existing record
                    if (questionMapping.FieldApiName__c == surveyMapping.MatchingField__c){
                        matchingField = questionMapping.FieldApiName__c;
                        matchingData = fieldValueMap.get(matchingField);
                        referenceFieldFound = true;
                    }
                }
            }
            if(referenceFieldFound){
                //checking if numeric or string matching data,to generate the query correctly
                Schema.SOAPType fieldSoapType = FieldDescribeMap.get(matchingField).getDescribe().getSOAPType();
                   matchingData = (fieldSoapType==Schema.SOAPType.Integer || fieldSoapType==Schema.SOAPType.Double)?
                                   matchingData:
                                   '\'' + matchingData + '\'';
                List <SObject> queryResult=Database.query('select '+ surveyMappedFields.replace(';',',') + 'Id from '+ surveyMapping.ObjectApiName__c + ' where '+ surveyMapping.MatchingField__c + '='+ matchingData);
                if (queryResult.size()>0){
                    mappedObject = queryResult[0];
                }

            }

            if (mappedObject==null){
                Schema.SObjectType targetType = Schema.getGlobalDescribe().get(surveyMapping.ObjectApiName__c);
                mappedObject = targetType.newSObject();
            }
            //assign the values to the SObject field
            for(String field:surveyMappedFields.split(';')){
                if(field!=null && field != ''){
                    if (fieldValueMap.get(field) == null){
                          mappedObject.put(field,null);
                    } else {
                        Schema.SOAPType fieldSoapType = FieldDescribeMap.get(field).getDescribe().getSOAPType();
                        Schema.DescribeFieldResult fieldType = FieldDescribeMap.get(field).getDescribe();
                         if(fieldSoapType == Schema.SOAPType.String || fieldType.getSOAPType() == Schema.SOAPType.Id){
                            mappedObject.put(field,fieldValueMap.get(field));
                        }else if(fieldSoapType == Schema.SOAPType.DateTime ){
                            mappedObject.put(field,DateTime.valueOf(fieldValueMap.get(field).replace('T', ' ')));
                        }else if(fieldSoapType == Schema.SOAPType.Date){
                            mappedObject.put(field,Date.valueOf(fieldValueMap.get(field)));
                        }else if(fieldSoapType == Schema.SOAPType.Double){
                            mappedObject.put(field,Double.valueOf(fieldValueMap.get(field)));
                        }else if (fieldSoapType == Schema.SOAPType.Integer){
                            mappedObject.put(field,Integer.valueOf(fieldValueMap.get(field)));
                        }else if (fieldSoapType == Schema.SOAPType.Boolean ){
                            mappedObject.put(field,(fieldValueMap.get(field).toLowerCase()=='true' || fieldValueMap.get(field)=='1'));
                        }
                    }
                }
            }
            upsert mappedObject;
        }
    }
    /*parse the string data from submission xml,the String list is divided like this
    [0] = y position
    [1] = x position
    [2] = alttitude position
    [3] = Approximation
    */
    public void parseLocation(String gpsData,Submission__c sa){
        String[] gpsDataList = gpsData.split(' ',0);
        if(gpsDataList.size() > 1){
            sa.gps_y__c = double.valueOf(gpsDataList[0]);
            sa.gps_x__c = double.valueOf(gpsDataList[1]);
            sa.gps_approximation__c = double.valueOf(gpsDataList[03]);
        }
    }
}
