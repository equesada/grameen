@isTest
private class ServicesControllerTest {

    /*Used to create a Contact with valid credentials to consume
    the services*/

    public static Contact CreateContactWithCredentials(String user,String pass){
        Contact con = new Contact(LastName='Robert',
                                    mobilesurveys_Username__c=user,
                                    mobilesurveys_Password__c=pass);
        insert con;
        return con;
    }
    //Test for the ParseSubmissionHeader method
    static testMethod void ParseSubmission() {
        //PREPARING THE TEST
        //For PPI surveys
        Contact interviewee = new Contact(LastName ='TestintervieweeMAP');
        insert interviewee;
        interviewee = [SELECT Name FROM Contact WHERE Id=:interviewee.Id limit 1];
        Survey__c survey = new Survey__c(Name='Survey',Status__c = 'Published',Gps_Location_Enabled__c=true,InterviewMapField__c = 'LastName');
        Survey__c survey2 = new Survey__c(Name='Survey2',Status__c = 'Invalid');
        List<Survey__c> surveys = new List<Survey__c>{survey,survey2};
        insert surveys;
        
        Question__c qSection    = new Question__c(Survey__c=survey.Id,Caption__c= 'section',Type__c = QuestionType.QUESTION_SECTION, Position__c = 1);
        insert new Question__c[]{qSection};

        Integer pos = 1;
        Question__c q1 = new Question__c(Position__c = pos++, Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'Username',Type__c = QuestionType.QUESTION_TEXT_SHORT);
        Question__c q2 = new Question__c(Position__c = pos++, Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'Last Name',Type__c = QuestionType.QUESTION_TEXT_SHORT);
        Question__c qContactDateTime = new Question__c(Position__c = pos++, Name = 'dateTimeQuestion',Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'Datetime',Type__c = QuestionType.QUESTION_DATETIME);
        Question__c q3 = new Question__c(Position__c = pos++, Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'select some',Type__c = QuestionType.QUESTION_CHECKBOX);
        Question__c qOpName = new Question__c(Position__c = pos++, Name = 'OppName',Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'Opportunity Name',Type__c = QuestionType.QUESTION_TEXT_SHORT);
        Question__c qOpCloseDate = new Question__c(Position__c = pos++, Name = 'OppCloseDate',Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'Opportunity Date',Type__c = QuestionType.QUESTION_DATE);
        Question__c qTotalOpportunityQuantity = new Question__c(Position__c = pos++, Name = 'OppQuantity',Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'Opportunity Number',Type__c = QuestionType.QUESTION_NUMBER);
        Question__c qOpStageName = new Question__c(Position__c = pos++, Name = 'OppStageName',Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'Opportunity Picklist',Type__c = QuestionType.QUESTION_RADIO);
        Question__c qOpProbability = new Question__c(Position__c = pos++, Name = 'OppProbability',Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'Opportunity Percent',Type__c = QuestionType.QUESTION_NUMBER);
        List<Question__c> questions= new List<Question__c>{q1,q2,q3,qTotalOpportunityQuantity,qOpStageName,qOpCloseDate,qOpName,qOpProbability,qContactDateTime};
        insert questions;

        Option__c opStageName = new Option__c(Name= 'Picklist',Caption__c = 'Picklist',Question__c = qOpStageName.Id,Position__c=1);
        Option__c opA = new Option__c(Name= 'opcion A',Question__c = q3.Id,Position__c=4);
        Option__c opB = new Option__c(Name= 'opcion B',Question__c = q3.Id,Position__c=5);
        List<Option__c> options= new List<Option__c>{opA,opB,opStageName};
        insert options;

        Contact c = new Contact(LastName = 'Robert',mobilesurveys_Id__c='4321');
        insert c;

        ScoringGroup__c sGroup1 = new ScoringGroup__c(Caption__c = 'Test#Scoring#Group',Survey__c=survey.id);
        ScoringGroup__c sGroup2 = new ScoringGroup__c(Caption__c = 'Test#Scoring#Group#2',Survey__c=survey.id);
        insert sGroup1;
        insert sGroup2;

        ScoreValue__c sValue1 = new ScoreValue__c(Option__c = opA.Id,ScoringGroup__c = sGroup1.Id, Value__c = 20);
        ScoreValue__c sValue2 = new ScoreValue__c(Option__c = opB.Id,ScoringGroup__c = sGroup1.Id, Value__c = 200);

        ScoreValue__c sValue3 = new ScoreValue__c(Option__c = opA.Id,ScoringGroup__c = sGroup2.Id, Value__c = 200);
        ScoreValue__c sValue4 = new ScoreValue__c(Option__c = opB.Id,ScoringGroup__c = sGroup2.Id, Value__c = 2000);

        insert new ScoreValue__c[]{sValue1,sValue2,sValue3,sValue4};
        //mapped object fields

        Account ac = new Account(Name = 'TheAccount',AccountNumber ='User');
        insert ac;
        Opportunity op = new Opportunity(Name='TheOp',StageName='Prospecting',TotalOpportunityQuantity=12.82,   CloseDate=date.today());
        insert op;
        //Survey Mapping,new Contact
        SurveyMapping__c sm1 = new SurveyMapping__c(Survey__c=survey.Id,ObjectApiName__c='Contact');
        //Survey Mapping with Matching field(Account already in DB)
        SurveyMapping__c sm2 = new SurveyMapping__c(Survey__c=survey.Id,ObjectApiName__c='Account',MatchingField__c='AccountNumber');
        //Survey Mapping opportunities
        SurveyMapping__c smOpportunity = new SurveyMapping__c(Survey__c=survey.Id,ObjectApiName__c='Opportunity',MatchingField__c='Name');
        insert new SurveyMapping__c[]{sm1,sm2,smOpportunity};

            //Question mapping for the contact
        QuestionMapping__c qm1 = new QuestionMapping__c(FieldApiName__c='LastName',Question__c=q2.Id,SurveyMapping__c=sm1.Id);
        QuestionMapping__c qm2 = new QuestionMapping__c(FieldApiName__c='mobilesurveys_Username__c',Question__c=q1.Id,SurveyMapping__c=sm1.Id);
        QuestionMapping__c qmDateTime = new QuestionMapping__c(FieldApiName__c='mobilesurveys_LastCheckInDate__c',Question__c=qContactDateTime.Id,SurveyMapping__c=sm1.Id);
            //Question mapping for the Account
        QuestionMapping__c qm3 = new QuestionMapping__c(FieldApiName__c='AccountNumber',Question__c=q1.Id,SurveyMapping__c=sm2.Id);
        QuestionMapping__c qm4 = new QuestionMapping__c(FieldApiName__c='Description',Question__c=q2.Id,SurveyMapping__c=sm2.Id);
            //Question mappings for opportunity
        QuestionMapping__c qmOpName = new QuestionMapping__c(FieldApiName__c='Name',Question__c=qOpName.Id,SurveyMapping__c=smOpportunity.Id);
        QuestionMapping__c qmOpTotalOpportunityQuantity = new QuestionMapping__c(FieldApiName__c='TotalOpportunityQuantity',Question__c=qTotalOpportunityQuantity.Id,SurveyMapping__c=smOpportunity.Id);
        QuestionMapping__c qmOpStageName = new QuestionMapping__c(FieldApiName__c='StageName',Question__c=qOpStageName.Id,SurveyMapping__c=smOpportunity.Id);
        QuestionMapping__c qmOpCloseDate = new QuestionMapping__c(FieldApiName__c='CloseDate',Question__c=qOpCloseDate.Id,SurveyMapping__c=smOpportunity.Id);
        QuestionMapping__c qmOpProbability = new QuestionMapping__c(FieldApiName__c='Probability',Question__c=qOpProbability.Id,SurveyMapping__c=smOpportunity.Id);
        insert new QuestionMapping__c[]{qm1,qm2,qm3,qm4,qmDateTime,qmOpName,qmOpTotalOpportunityQuantity,qmOpStageName,qmOpCloseDate,qmOpProbability};

        q1 = [select name from Question__c where id=:q1.Id][0];
        q2 = [select name from Question__c where id=:q2.Id][0];
        q3 = [select name from Question__c where id=:q3.Id][0];
        SurveyVersion__c surveyVersion = [select Status__c from SurveyVersion__c where Survey__c = :survey.Id order by Version__c desc];
        surveyVersion.Status__c = 'Published';
        update surveyVersion;

        String xml =
        '<?xml version=\'1.0\' ?><'+ survey.Name + ' id="' + survey.Id + '">'+
            '<start>2012-02-14T19:23:53.463</start>'+
            '<end>2012-02-14T19:24:40.816</end>'+
            '<today>2012-02-14</today>'+
            '<deviceid>356402040028589</deviceid>'+
            '<subscriberid >' + interviewee.Name + '</subscriberid >'+
            '<simid />'+
            '<location>70.0 80.0 0.0 1400.0</location>'+
            '<phonenumber />'+
            '<' + qSection.Name + '>' +
                '<'+ q1.Name +'>User</'+ q1.Name +'>'+
                '<'+ q2.Name +'>Martin</'+ q2.Name +'>'+
                '<'+ q3.Name +'>' + opA.Id + ' ' + OpB.Id + ' </'+ q3.Name +'>'+
                '<'+ qContactDateTime.Name +'>2012-02-14T19:23:53.463</'+ qContactDateTime.Name +'>'+
                '<'+ qOpName.Name +'>TheOp</'+ qOpName.Name +'>'+
                '<'+ qOpCloseDate.Name +'>2012-02-14</'+ qOpCloseDate.Name +'>'+
                '<'+ qTotalOpportunityQuantity .Name +'>9</'+ qTotalOpportunityQuantity .Name +'>'+
                '<'+ qOpStageName.Name +'>'+ opStageName.Id+ ' </'+ qOpStageName.Name +'>'+
                '<'+ qOpProbability.Name +'>30</'+ qOpProbability.Name +'>'+
            '</' + qSection.Name + '>' +
        '</'+ survey.Name + '>';
        //PERFORMING THE TEST
        //Published Survey case
        PageReference pr = Page.Submission;
        Test.setCurrentPage(pr);
        CreateContactWithCredentials('user1','thePass');
        ApexPages.currentPage().getHeaders().put('user','user1');
        ApexPages.currentPage().getHeaders().put('pass','thePass');
        ApexPages.currentPage().getHeaders().put('imei','24245215234');
        ApexPages.currentPage().getHeaders().put('surveyAnswer',xml);
        ApexPages.currentPage().getParameters().put('Last','true');
        ServicesController sc = new ServicesController();
        sc.submit();
        List<Submission__c> resultSA = [select id,name,survey__c,Date__c,Status__c,gps_x__c,gps_y__c,Interviewee__c from Submission__c where survey__c = :survey.Id];
        List<SubmissionScore__c> surveyTotalsScores = [SELECT Id,TotalScore__c FROM SubmissionScore__c WHERE  Submission__c =:resultSA[0].id order by TotalScore__c asc];
        //ASSERTIONS
            //Correct insert of the Submission
        system.assertNotEquals(0, resultSA.size());
        system.assertEquals(resultSA[0].survey__c,survey.Id);
        system.assertEquals(resultSA[0].gps_x__c ,80.0 );
        system.assertEquals(resultSA[0].gps_y__c ,70.0);
        system.assertEquals(resultSA[0].Date__c,Date.valueOf('2012-02-14'));
        system.assertEquals('<ID>'+ resultSA[0].Id + '</ID>',sc.Form);
        system.assertEquals(resultSA[0].Status__c,'Uploaded');
            //Correct insertion of the Answer and validations of the data
        List<Answer__c> resultQA = [select id,Question__c,Submission__c,Value__c, TextValue__c from Answer__c where Question__c = :q1.Id];
        system.assertNotEquals(0,resultQA.size());
        system.assertEquals(resultQA[0].Question__c,q1.Id);
        system.assertEquals(resultQA[0].Value__c,'User');
        system.assertEquals(resultQA[0].TextValue__c,'User');
        system.assertEquals(resultQA[0].Submission__c,resultSA[0].Id);
        List<Answer__c> resultQA1 = [select id,Question__c,Submission__c,Value__c, TextValue__c from Answer__c where Question__c = :q2.Id];
        system.assertEquals(resultQA1[0].Value__c,'Martin');
        system.assertEquals(resultQA1[0].TextValue__c,'Martin');
        system.assertNotEquals(0,resultQA1.size());
        system.assertEquals(resultQA1[0].Question__c,q2.Id);
        system.assertEquals(resultQA1[0].Submission__c,resultSA[0].Id);
        List<Answer__c> resultQA2 = [select id,Question__c,Submission__c,Value__c from Answer__c where Question__c = :q3.Id];
            //Correct insert of SubmissionScore
        system.assertEquals(surveyTotalsScores.size(),2);
        system.assertEquals(surveyTotalsScores[0].TotalScore__c, 220);
        system.assertEquals(surveyTotalsScores[1].TotalScore__c, 2200);
        system.assertEquals(2,resultQA2.size());
            //---------Validate Answers fields depending Question Types
        List<Answer__c> resultQA3 = [select id,Question__c,Submission__c,Value__c, DateValue__c, DateTimeValue__c from Answer__c where Question__c = :qContactDateTime.Id];//Date Time
        List<Answer__c> resultQA5 = [select id,Question__c,Submission__c,Value__c, DateValue__c from Answer__c where Question__c = :qOpCloseDate.Id];//Date
        List<Answer__c> resultQA6 = [select id,Question__c,Submission__c,Value__c, NumericValue__c from Answer__c where Question__c = :qTotalOpportunityQuantity.Id];//Number
        List<Answer__c> resultQA7 = [select id,Question__c,Submission__c,Value__c, TextValue__c from Answer__c where Question__c = :qOpStageName.Id];//Radio
            //Date Time
        system.assertEquals(resultQA3[0].DateTimeValue__c, dateTime.valueOf('2012-02-14 19:23:53.463'));
        system.assertEquals(resultQA3[0].DateValue__c, date.valueOf('2012-02-14 19:23:53.463'));
        system.assertEquals(resultQA3[0].value__c, '2012-02-14 19:23:53.463');

            //Date
        system.assertEquals(resultQA5[0].DateValue__c, date.valueOf('2012-02-14'));
        system.assertEquals(resultQA5[0].Value__c, '2012-02-14');
            //Number
        system.assertEquals(resultQA6[0].NumericValue__c, 9.000000);
        system.assertEquals(resultQA6[0].Value__c,'9');
            //Radio
        system.assertEquals(resultQA7[0].TextValue__c,  opStageName.Name);
        //----------------
        List<Contact> mappedContacts = [select LastName,mobilesurveys_Username__c,Contact.mobilesurveys_LastCheckInDate__c from Contact where LastName = 'Martin'];
            //Correct insertion of mapped fields
        system.assertEquals(1,mappedContacts.size());
        system.assertEquals('User',mappedContacts[0].mobilesurveys_Username__c);
        system.assertEquals(Datetime.valueof('2012-02-14 19:23:53.463'),mappedContacts[0].mobilesurveys_LastCheckInDate__c);
        List<Account> mappedAccount = [select Name,AccountNumber,Description from Account where AccountNumber = 'User'];
        List<Opportunity> mappedOpportunity = [select Name,IsPrivate,StageName,CloseDate,Probability,TotalOpportunityQuantity from Opportunity where Name='TheOp'];
            //Correct insertion of mapped fields(with matching field)
        system.assertEquals(1,mappedOpportunity.size());
        system.assertEquals(mappedOpportunity[0].Id, Op.Id);
        system.assertEquals(date.valueof('2012-02-14'),mappedOpportunity[0].CloseDate);
        system.assertEquals(9,mappedOpportunity[0].TotalOpportunityQuantity);
        system.assertEquals(30,mappedOpportunity[0].Probability);
        /* Tests interviewee mapping */
        system.assertEquals(interviewee.Id,resultSA[0].interviewee__c);

        //Invalid Survey case
        String xml2 =
        '<?xml version=\'1.0\' ?><'+ survey2.Name + ' id="' + survey2.Id + '">' +
            '<start>2012-02-14T19:23:53.463</start>'+
            '<end>2012-02-14T19:24:40.816</end>'+
            '<today>2012-02-14</today>'+
            '<deviceid>356402040028589</deviceid>'+
            '<subscriberid >' + c.mobilesurveys_Id__c + '</subscriberid >'+
            '<simid />'+
            '<phonenumber />'+

            '<' + qSection.Name + '>' +
                '<'+ q1.Name +'>23</'+ q1.Name +'>'+
                '<'+ q2.Name +'>Martin</'+ q2.Name +'>'+
            '<' + qSection.Name + '>' +
        '</'+ survey2.Name + '>';
        ApexPages.currentPage().getHeaders().put('surveyAnswer',xml2);

        ApexPages.currentPage().getParameters().put('Last','true');
        sc = new ServicesController();
        sc.submit();
        system.assertEquals('<error>This Survey is no longer available to receive answers</error>' ,sc.Form);

        //ASSERTIONS
            //No Submission inserted
        resultSA = [select id from Submission__c where survey__c = :survey2.Id];
        system.assertEquals(0, resultSA.size());
    }

    /* 
     * Test that PPI score calculation is calculated, and that the
     * submission is related to the corresponding PPITableLine__c objects.
     */
    static testMethod void testGeneratePPIScore(){
        // Create a contact to use as interviewee
        Contact interviewee = new Contact(LastName ='Robert',mobilesurveys_Id__c='4321');
        insert interviewee;
        interviewee = [SELECT Name, mobilesurveys_Id__c FROM Contact WHERE Id = :interviewee.Id LIMIT 1];

        // Create a survey
        ISO_Country__c country = new ISO_Country__c(Name = 'AA', ShortName__c = 'TEST');
        insert country;

        Survey__c survey = new Survey__c(
            Status__c               = 'Published',
            IsPPI__c                = true,
            CountryCode__c          = 'AA',
            Gps_Location_Enabled__c = true,
            PPIDataYear__c          = 2011,
            InterviewMapField__c    = 'mobilesurveys_Id__c');
        insert survey;

        Question__c section = new Question__c(Survey__c=survey.Id,Name='s',Caption__c='s',Type__c=QuestionType.QUESTION_SECTION,Position__c=1);
        insert section;
        Question__c question = new Question__c(
            Position__c = 1,
            Name        = 'q',
            Caption__c  = 'q',
            Parent__c   = section.Id,
            Survey__c   = survey.Id,
            Type__c     = QuestionType.QUESTION_CHECKBOX);
        insert question;
        Option__c option = new Option__c(Name='a',Question__c =question.Id,Position__c=1,PPIScore__c = 10);
        insert option;

        // Create a PPI Table
        PPITable__c table = new PPITable__c(Name='t'); insert table;
        PPITableDataSet__c dataset = new PPITableDataSet__c(PPITable__c = table.id,Survey__c=survey.Id); insert dataset;
        PPITableLine__c line1 = new PPITableLine__c(From__c=5,To__c=15,PPITableDataSet__c=dataset.Id,Probability__c=20);
        PPITableLine__c line2 = new PPITableLine__c(From__c=16,To__c=25,PPITableDataSet__c=dataset.Id,Probability__c=40);
        insert new PPITableLine__c[]{line1, line2};


        SurveyVersion__c surveyVersion3 = [SELECT Status__c FROM SurveyVersion__c WHERE Survey__c = :survey.Id ORDER BY Version__c DESC];
        surveyVersion3.Status__c = 'Published';
        update surveyVersion3;

        // Make a submission for the tested survey
        Test.startTest();
        String xml =
        '<?xml version=\'1.0\' ?><'+ survey.Name + ' id="' + survey.Id + '">'+
            '<start>2012-02-14T19:23:53.463</start>'+
            '<end>2012-02-14T19:24:40.816</end>'+
            '<today>2012-02-14</today>'+
            '<deviceid>356402040028589</deviceid>'+
            '<subscriberid >' + interviewee.mobilesurveys_Id__c + '</subscriberid >'+
            '<simid />'+
            '<phonenumber />'+

            '<' + section.Name + '>' +
                '<'+ question.Name +'>' + option.Id + '</'+ question.Name +'>'+
            '</' + section.Name + '>' +
        '</'+ survey.Name + '>';

        PageReference pr = Page.Submission;
        Test.setCurrentPage(pr);
        CreateContactWithCredentials('user1','thePass');
        ApexPages.currentPage().getHeaders().put('user','user1');
        ApexPages.currentPage().getHeaders().put('pass','thePass');
        ApexPages.currentPage().getHeaders().put('imei','24245215234');
        ApexPages.currentPage().getHeaders().put('surveyAnswer',xml);
        ApexPages.currentPage().getParameters().put('Last','true');
        ServicesController sc = new ServicesController();
        sc.submit();
        Test.stopTest();

        // Assert the PPI score was calculated
        Submission__c submission = [SELECT Id, Name, Survey__c, Date__c, Status__c, PPIScore__c FROM Submission__c WHERE Survey__c = :survey.Id];
        system.assertEquals(10,submission.PPIScore__c);

        // Assert that table line associations are made correctly
        SubmissionPPIValue__c[] lineAssociations = [SELECT Submission__c,PPITableLine__c FROM SubmissionPPIValue__c WHERE Submission__c = :submission.id];
        system.assertEquals(1, lineAssociations.size());
        system.assertEquals(line1.id, lineAssociations[0].PPITableLine__c);
    }

    /*
     * Tests MD5 functionality to match duplicates XML submissions.
     */

    static testmethod void testDuplicateSubmission(){
        Contact c = new Contact(LastName = 'Robert',mobilesurveys_Id__c='4321');
        insert c;
        Survey__c survey = new Survey__c(Name='Survey',Gps_Location_Enabled__c=true);
        insert survey;
        Question__c qSection = new Question__c(Survey__c=survey.Id,Caption__c= 'SECTIONNN',Type__c = QuestionType.QUESTION_SECTION);
        insert qSection;
        Question__c q1 = new Question__c(Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'Username',Type__c = QuestionType.QUESTION_TEXT_SHORT, Position__c = 1);
        Question__c q2 = new Question__c(Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'Last Name',Type__c = QuestionType.QUESTION_TEXT_SHORT, Position__c = 2);
        insert q1;insert q2;
        SurveyVersion__c surveyVersion = [select Status__c from SurveyVersion__c where Survey__c =:survey.Id];
        surveyVersion.Status__c = 'Published';
        update surveyVersion;
        String xml =
        '<?xml version=\'1.0\' ?><'+ survey.Name + ' id="' + survey.Id + '">'+
            '<start>2012-02-14T19:23:53.463</start>'+
            '<end>2012-02-14T19:24:40.816</end>'+
            '<today>2012-02-14</today>'+
            '<deviceid>356402040028589</deviceid>'+
            '<subscriberid >' + c.mobilesurveys_Id__c + '</subscriberid >'+
            '<simid />'+
            '<location>70.0 80.0 0.0 1400.0</location>'+
            '<phonenumber />'+
            '<' + qSection.Name + '>' +
                '<'+ q1.Name +'>User</'+ q1.Name +'>'+
                '<'+ q2.Name +'>Martin</'+ q2.Name +'>'+
            '</' + qSection.Name + '>' +
        '</'+ survey.Name + '>';
        //TEST 1 Submit the XML first time
        PageReference pr = Page.Submission;
        Test.setCurrentPage(pr);
        CreateContactWithCredentials('user1','thePass');
        ApexPages.currentPage().getHeaders().put('user','user1');
        ApexPages.currentPage().getHeaders().put('pass','thePass');
        ApexPages.currentPage().getHeaders().put('imei','24245215234');
        ApexPages.currentPage().getHeaders().put('surveyAnswer',xml);
        ApexPages.currentPage().getParameters().put('Last','true');
        ServicesController sc = new ServicesController();
        sc.submit();
        //TEST 2 Submit the XML second time
        sc = new ServicesController();
        sc.submit();
        //Although there were two submission only one should have been created.
        List<Submission__c> resultSA = [select id,name,survey__c,Date__c,Status__c,gps_x__c,gps_y__c from Submission__c where survey__c = :survey.Id];
        System.assertEquals(1,resultSA.size());
    }
    private static Survey__c prepareRetrievalTest(){

        Survey__c survey = new Survey__c(
            Name = 'Survey');
        insert survey;
        SurveyVersion__c surveyVersion = [select Status__c from SurveyVersion__c where Survey__c =:survey.Id];
        surveyVersion.Status__c = 'Published';
        update surveyVersion;
        Attachment att = new Attachment(
            Name = XformCreator.XFORM_ATTACHMENT,
            ParentId = survey.Id,
            Body = blob.valueOf('Body of Document'));
        insert att;

        return survey;
    }

    private static void prepareCredentials(){
        CreateContactWithCredentials('user1','thePass');
        ApexPages.currentPage().getHeaders().put('user','user1');
        ApexPages.currentPage().getHeaders().put('pass','thePass');
        ApexPages.currentPage().getHeaders().put('imei','24245215234');
    }
    static testMethod void correctRetrieval(){
        //param provided and document exists
        Survey__c survey = prepareRetrievalTest();

        PageReference pageRef = Page.formList;
        Test.setCurrentPageReference(pageRef);
        ServicesController sc = new ServicesController();
        Apexpages.currentPage().getParameters().put('id',survey.Id);
        //Test of a correct Id with a correct document
        sc.getSurvey(survey.Id);
        system.assertEquals( sc.Form, 'Body of Document' );
    }
    static testMethod void missingParam(){
        //no id provided to retrieve
        prepareRetrievalTest();

        PageReference pageRef = Page.formList;
        ServicesController sc = new ServicesController();
        //run the process without id param
        sc.process();
        system.assertNotEquals('Body of Document',sc.Form);
    }
    static testMethod void missingDocument(){
        //document does not exist
        Survey__c survey = prepareRetrievalTest();

        prepareCredentials();
        Apexpages.currentPage().getParameters().put('id',survey.Id);
        PageReference pageRef = Page.formList;
        delete survey;
        ServicesController sc = new ServicesController();
        sc.process();
        system.assertEquals( sc.msg, sc.ERR_NO_SURVEY );
    }
    static testMethod void tooManyDocuments(){
        //there are more than one document for this id
        Survey__c survey = prepareRetrievalTest();

        Attachment att2 = new Attachment(
            Name = XformCreator.XFORM_ATTACHMENT,
            ParentId = survey.Id,
            Body = blob.valueOf('Body of Second Document'));
        insert att2;

        prepareCredentials();
        Apexpages.currentPage().getParameters().put('id',survey.Id);

        PageReference pageRef = Page.formList;
        ServicesController sc = new ServicesController();
        sc.process();
        String docName = survey.Id + '.xml';
        Attachment[] atts = [select Id from Attachment where ParentId = :survey.Id and Name = :XformCreator.XFORM_ATTACHMENT];
        system.assertEquals(1,atts.size());
    }

    static testMethod void TestLogin(){
        Profile standardProfile = [SELECT Id FROM Profile WHERE Name = 'Standard User' limit 1];
        User standardUser =  new User(
            UserName = 'test-user@test-company.com',
            FirstName = 'Test-First-Name',
            LastName = 'Test-Last-Name',
            Alias = 'test',
            Email = 'test-user@test-company.com',
            EmailEncodingKey = 'UTF-8',
            LanguageLocaleKey = 'en_US',
            LocalesIdKey = 'en_US',
            TimezonesIdKey = 'America/Los_Angeles',
            ProfileId = standardProfile.Id
            );
        insert standardUser;
        Contact loggedUser = CreateContactWithCredentials('user1','thePass');
        Device__c dev;

        // Run the method as a user different to the one that created the contact.
        // The contact is private (because it has no account).
        // Even through the contact is not visible for this new user, the Login should be possible.
        system.runAs(standardUser){
            ServicesController.LoginResult result;
            ServicesController sc = new ServicesController();

            // Check wrong login
            result = sc.login('userX', 'thePass', '3453634');
            system.AssertEquals(servicesController.INVALID_AUTHENTICATION_LOGIN,result.result);

            // Check Inactive device
            dev = new Device__c(Name='141434223',imei__c = '141434223',Status__c='Inactive');
            insert dev;
            result = sc.login('user1', 'thePass', '141434223');
            system.AssertEquals(servicesController.INVALID_DEVICE_LOGIN,result.result);

            //Check LastCheckIn date and device in Contact(in a Correct Login)
            dev.Status__c = 'Active';
            update dev;
            result = sc.login('user1', 'thePass', '141434223');
            System.assertEquals(servicesController.CORRECT_LOGIN,result.result);
        }

        // This code is out the system.RunAs, since I need to be admin to be able to seee the contact.
        loggedUser = [select mobilesurveys_LastCheckInDevice__c,mobilesurveys_LastCheckInDate__c,Id from Contact where Id = :loggedUser.Id];
        system.assertEquals(dev.Id,loggedUser.mobilesurveys_LastCheckInDevice__c);
        system.assertNotEquals(loggedUser.mobilesurveys_LastCheckInDate__c,null);
    }
}