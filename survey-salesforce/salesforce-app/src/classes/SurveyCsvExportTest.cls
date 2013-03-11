@isTest
private class SurveyCsvExportTest {


    /*Method to prepare the data for performing a test.inserts survey
    question and their options */
    public static Contact createContactWithCredentials(String user,String pass){
        Contact newContact = new Contact( LastName='Robert',
                            mobilesurveys_Username__c=user,
                            mobilesurveys_Password__c=pass);

        insert newContact;
        return [select Id, Name from Contact where Id = :newcontact.Id];
    }

    class TestData{
        public String[] xml;
        public String expected;
        public Id surveyId;
    }

    public static TestData PrepareData(Contact loggedContact){
    //PREPARING THE DATA FOR THE TEST

        Survey__c survey = new Survey__c(Name='Survey',InterviewMapField__c='mobileSurveys_Id__c');
        insert survey;
        Question__c qSection = new Question__c(Survey__c=survey.Id, Name='qsect', Caption__c= 'SECTIONNN',Type__c = QuestionType.QUESTION_SECTION);
        insert qSection;
        Question__c q0 = new Question__c(Parent__c= qSection.Id,Survey__c=survey.Id, Name='qstat', Caption__c= 'Static Content',Type__c = QuestionType.QUESTION_STATIC,Position__c=1);
        Question__c q1 = new Question__c(Parent__c= qSection.Id,Survey__c=survey.Id, Name='qshrt', Caption__c= 'Age',Type__c = QuestionType.QUESTION_TEXT_SHORT,Position__c=2);
        Question__c q2 = new Question__c(Parent__c= qSection.Id,Survey__c=survey.Id, Name='qlong', Caption__c= 'Last Name',Type__c = QuestionType.QUESTION_TEXT_LONG,Position__c=3);
        Question__c q3 = new Question__c(Parent__c= qSection.Id,Survey__c=survey.Id, Name='qchkx', Caption__c= 'select some',Type__c = QuestionType.QUESTION_CHECKBOX,Position__c=4);
        Question__c q4 = new Question__c(Parent__c= qSection.Id,Survey__c=survey.Id, Name='qnum', Caption__c= 'number',Type__c = QuestionType.QUESTION_NUMBER,Position__c=5);
        Question__c q5 = new Question__c(Parent__c= qSection.Id,Survey__c=survey.Id, Name='qdate', Caption__c= 'date',Type__c = QuestionType.QUESTION_DATE,Position__c=6 );
        Question__c q6 = new Question__c(Parent__c= qSection.Id,Survey__c=survey.Id, Name='qdateTime', Caption__c= 'dateTime',Type__c = QuestionType.QUESTION_DATETIME,Position__c=7);
        insert new Question__c[]{q0,q1,q2,q3,q4,q5,q6};

        Option__c opA = new Option__c(Name= 'opcion A',Caption__c='opcion A',Question__c = q3.Id,Position__c=1);
        Option__c opB = new Option__c(Name= 'opcion B',Caption__c='opcion B',Question__c = q3.Id,Position__c=2);
        List<Option__c> options= new List<Option__c>{opA,opB};
        insert options;
        Contact c = new Contact(LastName = 'Robert', mobileSurveys_Id__c='ab12');
        insert c;
        c = [select Id, Name, mobileSurveys_Id__c from Contact where Id = :c.Id];
        qSection = [select name from Question__c where id=:qSection.Id][0];

        ScoringGroup__c sGroup = new ScoringGroup__c(Caption__c = 'score1', Survey__c = survey.Id);
        insert sGroup;
        ScoreValue__c sValue1 = new ScoreValue__c(Option__c = opA.Id, ScoringGroup__c = sGroup.Id, Value__c = 1);
        ScoreValue__c sValue2 = new ScoreValue__c(Option__c = opB.Id, ScoringGroup__c = sGroup.Id, Value__c = 2);
        insert new ScoreValue__c[]{sValue1, sValue2};
        //Clean Devices and add new one
        List<Device__c> allDevices = [SELECT Id FROM Device__c Where Id!=null];
        delete allDevices;
        Device__c usedDevice = new Device__c(Name='24245215234',Imei__c='24245215234',Status__c='Active');
        insert usedDevice;
        SurveyVersion__c surveyVersion = [select Status__c from SurveyVersion__c where Survey__c =:survey.Id];
        surveyVersion.Status__c = 'Published';
        update surveyVersion;
        TestData result = new TestData();
        //XML 1
        result.xml = new String[]{};
        for(integer i=0;i<3;i++){
            result.xml.add('<?xml version=\'1.0\' ?><'+ survey.Name + ' id="' + survey.Id + '">'+
            '<start>2012-02-14T19:23:53.463</start>'+
            '<end>2012-02-14T19:24:40.816</end>'+
            '<today>2012-02-14</today>'+
            '<deviceid>356402040028589</deviceid>'+
            '<subscriberid >' + c.mobileSurveys_Id__c + '</subscriberid >'+
            '<simid />'+
            '<phonenumber />'+
            '<' + qSection.Name + '>' +
                '<'+ q0.Name +' />' +
                '<'+ q1.Name +'>2'+ i +'</'+ q1.Name +'>'+
                '<'+ q2.Name +'>Martin</'+ q2.Name +'>'+
                '<'+ q3.Name +'>' + opA.Id + ' ' + OpB.Id + ' </'+ q3.Name +'>'+
                '<'+ q4.Name + '>' + 10 + '</' + q4.Name + '>' +
                '<'+ q5.Name + '>' + '2012-02-14' + '</' + q5.Name + '>' +
                '<'+ q6.Name + '>' + '2012-02-14T19:23:53.463' + '</' + q6.Name + '>' +
            '</' + qSection.Name + '>' +
        '</'+ survey.Name + '>');
        }
        result.expected =
        '"startDate","endDate","Date","Device","gps_x","gps_y","Interviewee","Surveyor",' +
        '"' + q1.Name + '",' +
        '"' + q2.Name + '",' +
        '"' + q3.Name + '",' +
        '"' + q4.Name + '",' +
        '"' + q5.Name + '",' +
        '"' + q6.Name + '",' +
        '"' + sGroup.Caption__c + '",' + '\n' +
        '"' + DateTime.valueOf('2012-02-14 19:23:53') + '",' + '"' + DateTime.valueOf('2012-02-14 19:24:40') + '",' +
        '"2012-02-14 00:00:00",' +
        '"' + usedDevice.Id +'","","","'+ c.Name + '","' + loggedContact.Name + '","'+ '20",'+
        '"Martin","' + opA.Caption__c + ';' + OpB.Caption__c + '",' + '"10",'+ '"2012-02-14",'+ '"2012-02-14 19:23:53.463",' + '"3",' + '\n';

        result.surveyId = survey.Id;
        return result;
    }

    private static void loadContactCredentialsOnPage(String user, String pass, String imei){
        ApexPages.currentPage().getHeaders().put('user',user);
        ApexPages.currentPage().getHeaders().put('pass',pass);
        ApexPages.currentPage().getHeaders().put('imei',imei);
    }

    /*Test for a single case of csv Creation*/
     static testMethod void exportAllSubmissions() {
         //PERFORMING THE TEST
         PageReference pr = Page.Submission;
        Test.setCurrentPage(pr);
        Contact loggedContact = createContactWithCredentials('user1','thePass');
        TestData data = prepareData(loggedContact);
        ApexPages.currentPage().getHeaders().put('surveyAnswer',data.xml[0]);
        ApexPages.currentPage().getParameters().put('Last','true');

        //create a valid user for submission
        loadContactCredentialsOnPage('user1','thePass','24245215234');

        ServicesController sc = new ServicesController();
        sc.submit();
        SurveyCsvExport csvCreator = new SurveyCsvExport();
        PageReference downpagePage = Page.downloadCsv;
        Test.setCurrentPage(downpagePage);
        ApexPages.currentPage().getParameters().put('surveyId',data.surveyId);

        system.assertEquals(data.expected,csvCreator.getCsvString());

     }

     static testMethod void exportSelectedSubmissions() {
         //PERFORMING THE TEST
        PageReference pr = Page.Submission;
        Test.setCurrentPage(pr);
        //clean users and create a valid user for submission
        delete ([select Id from Contact where mobilesurveys_Username__c= 'user1']);

        Contact loggedContact = createContactWithCredentials('user1','thePass');
        TestData data = prepareData(loggedContact);
        ApexPages.currentPage().getParameters().put('Last','true');


        loadContactCredentialsOnPage('user1','thePass','24245215234');

        // Submit three times, but I'm only going to export two of them
        ServicesController sc = new ServicesController();
        for(string s:data.xml){
            ApexPages.currentPage().getHeaders().put('surveyAnswer',s);
            sc.submit();
        }
        Submission__c[] submissions = [select Id,survey__c,
                                            startDate__c ,
                                            endDate__c,
                                            Date__c,
                                            Device__c,
                                            gps_x__c,
                                            gps_y__c,
                                            surveyor__c, surveyor__r.Name,
                                            interviewee__c, interviewee__r.Name
                                            from Submission__c where Survey__c = :data.surveyId];
        system.assertEquals(3, submissions.size());

        SurveyCsvExport csvCreator = new SurveyCsvExport();
        PageReference downpagePage = Page.downloadCsv;
        Test.setCurrentPage(downpagePage);
        ApexPages.currentPage().getParameters().put('selectedSurveysAnswerId',submissions[0].Id + ',' + submissions[1].Id);

        // Assert the result only has three lines (1 header + 2 rows)
        system.assertEquals(3, csvCreator.getCsvString().split('\n',0).size());
     }
}