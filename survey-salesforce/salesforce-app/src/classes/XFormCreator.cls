public with sharing class XFormCreator {

    public String url { get; set; }

    public String Message{get; set; }
    public static final String ERR_NO_PARAM = 'No Survey found';
    public static final String URL_XMLLISTSURVEYS = 'http://openrosa.org/xforms/xformsList';
    public static final String XFORM_ATTACHMENT = 'xform.xml';
    public static final String XFORM_INITIALPATH= '/data';
    public static final String STATUS_PUBLISHED = 'Published';
    public static survey__c currentSurvey;


    //creates a binding node for any type of question
    public static void createBindingNode(dom.Xmlnode rootNode,String nodeset,String xmlType,String preload,String preloadParams){
          dom.Xmlnode bindNode = rootNode.addChildElement('bind', null, null);
          bindNode.setAttribute('nodeset',nodeset);
          bindNode.setAttribute('type',XmlType);
          bindNode.setAttribute('jr:preload',preload);
          bindNode.setAttribute('jr:preloadParams',preloadParams);
    }

    //sets the standard header binding nodes of the xform
    public static void setHeaderBindings(dom.Xmlnode rootNode){
          createBindingNode(rootNode,'/data/start','dateTime','timestamp','start');
          createBindingNode(rootNode,'/data/end','dateTime','timestamp','end');
          createBindingNode(rootNode,'/data/today','date','date','today');
          createBindingNode(rootNode,'/data/deviceid','string','property','devicesid');
          if(currentSurvey.InterviewMapField__c != '' && currentSurvey.InterviewMapField__c != null){
              dom.Xmlnode suscriberNode = rootNode.addChildElement('bind', null, null);
              suscriberNode.setAttribute('nodeset','/data/subscriberid');
              suscriberNode.setAttribute('type','string');
              suscriberNode.setAttribute('required','true()');
          }
          createBindingNode(rootNode,'/data/simid','string','property','simserial');
          dom.Xmlnode bindNode = rootNode.addChildElement('bind', null, null);
        bindNode.setAttribute('nodeset','/data/location');
        bindNode.setAttribute('type','geopoint');
          createBindingNode(rootNode,'/data/phonenumber','string','property','phonenumber');
    }

    //sets the standard header model nodes of the xform
    public static void setHeaderModel(dom.Xmlnode rootNode){
          rootNode.addChildElement('start', null, null);
          rootNode.addChildElement('end', null, null);
          rootNode.addChildElement('today', null, null);
          rootNode.addChildElement('deviceid', null, null);
          if(currentSurvey.InterviewMapField__c != '' && currentSurvey.InterviewMapField__c != null)
            rootNode.addChildElement('subscriberid', null, null);
          rootNode.addChildElement('simid', null, null);
          rootNode.addChildElement('location', null, null);
          rootNode.addChildElement('phonenumber', null, null);
    }

    public Attachment createDocument(Survey__c sur){
        currentSurvey = sur;
        // Remove any previously existing xforms
        Attachment[] atts = [Select Id from Attachment where ParentId = :sur.Id and Name = :XFORM_ATTACHMENT];
        if (atts.size() > 0) delete atts;

        String surveyNameToRender = (sur.Alias__c=='' ||sur.Alias__c==null)?sur.Name:sur.Alias__c;
        String xform = this.getXFormFromSurvey(sur, surveyNameToRender).ToXmlString();

        Attachment att = new Attachment(
            Name = XFORM_ATTACHMENT,
            ParentId = sur.Id,
            Body = Blob.valueOf(xform)
            );
        insert att;

        sur.XForm_Status__c = 'DEFAULT';
        sur.Saving_XForm__c = true;
        update sur;

        this.Message ='XForm generated correctly.';
        this.url='/servlet/servlet.FileDownload?file='+att.Id;
        return att;
    }

    public PageReference createDocument(){
        //Obtains the surveyId or the surveyId from the url and create the correct document.
        Id surveyId = Apexpages.currentPage().getParameters().get('SurveyId');

        //if the parameter SurveyId Exist create that document,else use the Surveyid param
        if (surveyId != null){
            ServicesController sc = new ServicesController();
            sc.getSurvey(surveyId);
        }else{
            //in case that surveyId is not found in the page
            this.Message= ERR_NO_PARAM;
        }
        return null;
    }

    // This method transforms the survey__c in their object implementation and starts the transformation to XML
    public dom.Document getXFormFromSurvey(Survey__c sur,String surveyTitle){
        //Getting all the question of the survey
        List<Question__c> questions = [Select SkipLogicBehavior__c, SkipLogicOperator__c, Name, Hint__c, Caption__c, Type__c,
                                       Maximum__c,  Minimum__c, Parent__r.Id, Parent__c, Required__c, SamePage__c
                                       from Question__c
                                       where Survey__r.Id = :sur.Id
                                       order by Position__c];
        Survey survey = new Survey();
        survey.Name= surveyTitle;
        survey.SurveyId = sur.Id;
        Map<Id,QuestionType.Section> Sections = new Map<Id,QuestionType.Section>();
        List<Option__c> options = [select Name,Caption__c,Question__r.Name,Position__c from Option__c where Question__r.Survey__c =:sur.Id order by Position__c];

        //used to map (question Id,Path Id)
        Map<Id,String> questionPaths= new Map<Id,String>();

        //Iterate over the question
        for(Question__c q: questions ){
             // If the question is of type Section,add it to
             // the Section Map
             if ( q.Type__c == QuestionType.QUESTION_SECTION){

                QuestionType.Section question = new QuestionType.Section();
                question.Name = String.valueOf(q.Name);
                question.Caption =(q.Caption__c == null) ? q.Name : q.Caption__c;
                question.Hints = (q.Hint__c == null) ? '' : q.Hint__c;
                question.ShowInSinglePage = q.SamePage__c;
                survey.questions.add(question);
                Sections.put(q.Id,question);
             }
         }

         for(Question__c q: questions){
            // Check the question type,create that type of question,and add it
            // to his sections question list

            if( q.Type__c == QuestionType.QUESTION_STATIC || q.Type__c == QuestionType.QUESTION_TEXT_SHORT || q.Type__c == QuestionType.QUESTION_TEXT_LONG ||q.Type__c == QuestionType.QUESTION_NUMBER_INTEGER|| q.Type__c == QuestionType.QUESTION_NUMBER_DECIMAL ||q.Type__c == QuestionType.QUESTION_NUMBER || q.Type__c == QuestionType.QUESTION_DATE || q.Type__c == QuestionType.QUESTION_DATETIME){
                QuestionType.SimpleInput question = new QuestionType.SimpleInput();
                question.Type = q.Type__c;
                question.Name = String.valueOf(q.Name);
                question.Maximum=q.Maximum__c;
                question.Minimum=q.Minimum__c;
                question.logicBehavior = q.SkipLogicBehavior__c;
                question.logicOperator = q.SkipLogicOperator__c;
                question.Caption =(q.Caption__c== null)? q.Name : q.Caption__c;
                question.Hints = (q.Hint__c== null)? '' : q.Hint__c;
                question.Required = q.Required__c != null && q.Required__c;
                question.Path = XFORM_INITIALPATH +'/' + sections.get(q.Parent__r.Id).Name;
                questionPaths.put(q.Id,question.Path +'/'+ q.Name);
                question.Id= q.Id;
                sections.get(q.Parent__r.Id).sectionQuestions.add(question);

            } else if (q.Type__c == QuestionType.QUESTION_RADIO || q.Type__c == QuestionType.QUESTION_CHECKBOX){
                QuestionType.SelectionQuestion question = new QuestionType.SelectionQuestion(q.Name,q.Type__c,options);
                question.Caption =(q.Caption__c== null)? q.Name : q.Caption__c;
                question.Hints = (q.Hint__c== null)? '' : q.Hint__c;
                question.logicBehavior = q.SkipLogicBehavior__c;
                question.logicOperator = q.SkipLogicOperator__c;
                question.Required = q.Required__c != null && q.Required__c;
                question.Path = XFORM_INITIALPATH + '/' + sections.get(q.Parent__r.Id).Name;
                questionPaths.put(q.Id,question.Path +'/'+q.Name);
                question.Id= q.Id;
                sections.get(q.Parent__r.Id).sectionQuestions.add(question);
            }
        }
        return survey.getXForms(questionPaths,sur);
    }
    public dom.Document getXmlAllSurveys(String UrlBody,Id loggedContactId){
        //Filter the downloadable surveys for the group this contact belongs to
        List<ContactGroupSurveyAssignment__c> assignmentsIds = (List<ContactGroupSurveyAssignment__c>) DBFullAccess.QuerySObject('select Survey__c from ContactGroupSurveyAssignment__c where ContactGroup__c in (select ContactGroup__c from ContactGroupMember__c where Contact__c =\'' + loggedContactId + '\')');
        Set<Id> surveyIds = new Set<Id>();
        for (ContactGroupSurveyAssignment__c cgsa:assignmentsIds){
            surveyIds.add(cgsa.Survey__c);
        }

        // Get all published surveys
        List<Survey__c> allSurveyList = null;
        if (!Test.isRunningTest()){
            allSurveyList = [Select Name, Id, Alias__c, Close_Message__c From Survey__c WHERE Status__c = :STATUS_PUBLISHED and Id in :surveyIds ORDER BY CreatedDate DESC];
        } else {
            allSurveyList = [Select Name, Id, Alias__c, Close_Message__c From Survey__c WHERE Name LIKE '__test__%' and Status__c = :STATUS_PUBLISHED and Id in :surveyIds ORDER BY CreatedDate DESC];
        }

        //Map used to list only the last
        //published version of a survey
        Map<Id,Survey__c> SurveysMap = new Map<Id,Survey__c>();
        dom.Document doc= new dom.Document();
        dom.Xmlnode xmlRoot= doc.createRootElement('xforms',URL_XMLLISTSURVEYS,'');
        String downloadUrl = (Site.getName() != null ? Site.getCurrentSiteUrl().replaceFirst('/$','') : '') + Page.formList.getUrl(); // base Url for downloading any survey

        // Get the latest version for each survey
        Map<Id,Decimal> versionNumbers = new Map<Id,Decimal>();
        for(SurveyVersion__c version : [SELECT Id, Version__c, Survey__c
                                        FROM SurveyVersion__c
                                        WHERE Status__c = :STATUS_PUBLISHED
                                        ORDER BY Survey__c ASC, Version__c ASC]){
            versionNumbers.put(version.Survey__c, version.Version__c);
        }
        for (Survey__c sur: allSurveyList)
        {
            if (!(SurveysMap.containsKey(sur.Id))){
                dom.Xmlnode xmlForm = xmlRoot.addChildElement('xform',null,null);
                SurveysMap.put(sur.Id,sur);
                String surveyLabel = ((sur.Alias__c=='') || (sur.Alias__c == null) ) ? sur.Name : sur.Alias__c;
                xmlForm.addChildElement('formID',null,null).addTextNode(sur.Id);
                xmlForm.addChildElement('name',null,null).addTextNode(surveyLabel);
                xmlForm.addChildElement('majorMinorVersion',null,null);
                xmlForm.addChildElement('version',null,null).addTextNode(String.valueOf(versionNumbers.get(sur.Id)));
                xmlForm.addChildElement('hash',null,null);
                xmlForm.addChildElement('downloadUrl',null,null).addTextNode(downloadUrl + '?id=' + sur.Id);
            }
        }
        return doc;
    }

    // used as a tag to mark a class as XFormsSerializable
    public interface  IXFormsSerializable{
        /*Returns the declaration on the model tag*/

        void getXFormsModel(dom.Xmlnode node,Map<Id,String> questionPaths);

        /*Returns the xml that goes into de body of the */
        void getXFormBody(dom.Xmlnode node,Survey__c sur);
    }

    // Represents an survey with convient method to make the xForms convertion
    public class Survey implements IXFormsSerializable{

        public Id SurveyId{ get; set; }
        public String Name { get; set; }
        public List<QuestionType.IQuestion> questions = new List<QuestionType.IQuestion>();
        public String ValidName {get {return this.Name.replace(' ','_');}}

        /* This method returns the part of the model of the survey */
        public void getXFormsModel(dom.Xmlnode xmlRoot,Map<Id,String> questionPaths){

            dom.Xmlnode xmlHead = xmlRoot.addChildElement('h:head',null,null);
            xmlHead.addChildElement('h:title',null,null).addTextNode(Name);
            dom.Xmlnode xmlModel = xmlHead.addChildElement('model',null,null);
            dom.Xmlnode xmlInstance = xmlModel.addChildElement('instance',null,null);
            dom.Xmlnode xmlData = xmlInstance.addChildElement('data',null,null);
            xmlData.setAttribute('id',SurveyId);
            setHeaderModel(xmlData);
            setHeaderBindings(xmlModel);

            //List of conditions used to generate the Xform
            List<SkipCondition__c> skipConditions = [
                SELECT Name,Parent__c,SourceQuestion__c,Value__c,Condition__c,Negate__c,SourceQuestion__r.Type__c
                FROM SkipCondition__c
                WHERE SourceQuestion__r.Survey__c =:surveyId
                ORDER BY Id];

            for(QuestionType.IQuestion question : this.questions){
                question.getXFormsModel(xmlData);
                question.getBindings(xmlModel,skipConditions,questionPaths);
            }
            // Insert close message at the end of the survey, if present.
            if (currentSurvey.Close_Message__c != null && currentSurvey.Close_Message__c != ''){
                //Adds a Node for closemessage into the Model Node.
                xmlData.addChildElement('closemessage', null, null);
                //Adds a Node for closemessage into the Bind Node.
                dom.Xmlnode closemessageNode = xmlModel.addChildElement('bind', null, null);
                closemessageNode.setAttribute('nodeset','/data/closemessage');
                closemessageNode.setAttribute('type','string');
                closemessageNode.setAttribute('readonly','true()');
            }
        }

        public void getXFormBody(dom.Xmlnode xmlRoot,Survey__c survey){
            dom.Xmlnode xmlBody = xmlRoot.addChildElement('h:body',null,null);

            // Create the first group, if we have to ask for interviewee or location
            if((currentSurvey.InterviewMapField__c != null && currentSurvey.InterviewMapField__c != '') || survey.Gps_Location_Enabled__c){

                // Create the group that will contain interviewee and location questions
            dom.Xmlnode xmlStandardQuestions = XmlBody.addChildElement('group',null,null);
            xmlStandardQuestions.setAttribute('appearance','field-list');

                // Add interviewee question if it's being asked for
                if(currentSurvey.InterviewMapField__c != null && currentSurvey.InterviewMapField__c != '' ){
            dom.Xmlnode xmlIntervieweeInput = xmlStandardQuestions.addChildElement('input',null,null);
            xmlIntervieweeInput.setAttribute('ref','/data/subscriberid');
            xmlIntervieweeInput.addChildElement('label',null,null).addTextNode('Who are you interviewing?');
            xmlIntervieweeInput.addChildElement('hint',null,null).addTextNode('Please enter the interviewee\'s Id');
                }

                // Add geo-location question if it's being asked for
            if (survey.Gps_Location_Enabled__c){
                dom.Xmlnode xmlLocationInput = xmlStandardQuestions.addChildElement('input',null,null);
                xmlLocationInput.setAttribute('ref','/data/location');
                if (survey.Gps_ShowMap__c){
                    xmlLocationInput.setAttribute('appearance','maps');
                }
                xmlLocationInput.addChildElement('label',null,null).addTextNode('GPS Location');
                xmlLocationInput.addChildElement('hint',null,null).addTextNode('This will capture your current location');
                }
            }


            for(QuestionType.IQuestion q :this.questions){
                q.getXformBody(xmlBody);
            }

            // Insert close message at the end of the survey, if present.
            if (survey.Close_Message__c != null && survey.Close_Message__c != ''){
                //Add the close message Node into the Data Node
                dom.Xmlnode xmlCloseMessage = XmlBody.addChildElement('input',null,null);

                //The close message will been showed as HINT text.
                xmlCloseMessage.addChildElement('hint',null,null).addTextNode(survey.Close_Message__c);
                xmlCloseMessage.setAttribute('ref','/data/closemessage');
            }
        }

        //method to build XML to list this survey
        public dom.Document getXForms(Map<Id,String> questionPaths,Survey__c survey) {
            currentSurvey = survey;
            dom.Document doc= new dom.Document();
            dom.Xmlnode xmlRoot = doc.createRootElement('h:html',null,null);
            xmlRoot.setNamespace('jr','http://openrosa.org/javarosa');
            xmlRoot.setNamespace('h','http://www.w3.org/1999/xhtml');
            this.getXFormsModel(xmlRoot,questionPaths);
            this.getXFormBody(xmlRoot,survey);
            return doc;
        }
    }
}