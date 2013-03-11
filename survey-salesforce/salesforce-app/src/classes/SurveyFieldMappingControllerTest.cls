/**
 * This class will test:
 *     -Proper display of the question List.
 *     -Proper Load of the objects dropdown lists.
 *     -Proper Load of the Field dropdown lists.
 *     -Proper Save of the chosen data.
 */
@isTest
private class SurveyFieldMappingControllerTest {
    static final string OBJ_CONTACT = 'Contact';
    static final string OBJ_ACCOUNT = 'Account';
    static testMethod void mainTest() {
        LicenseSettings__c testWithLicense = newLicense(false,'Test1');
        insert testWithLicense;
        // Needed enviroment
        Survey__c survey1 = newSurvey('FirstSurvey');
        Question__c sec1 = newQuestion(survey1, null, 'Section', 'section1', 'section1', 1);
        Question__c sec2 = newQuestion(survey1, null, 'Section', 'section2', 'section2', 2);
        insert new Question__c[]{sec1,sec2};

        Question__c qlname = newQuestion(survey1,sec1,'text-short','lname','Last name',1);
        Question__c qbday = newQuestion(survey1,sec1,'date-date','bday','Birthdate',2);
        Question__c qdept = newQuestion(survey1,sec1,'text-short','dept','Department',3);
        Question__c qdesc = newQuestion(survey1,sec2,'text-short','desc','Description',1);
        Question__c qasst = newQuestion(survey1,sec2,'text-short','asst','Assistant Name',2);
        insert new Question__c[]{qlname,qbday,qdept,qdesc,qasst};

        //This List is right ordered, sections by positions and the corresponding questions after each section.
        //Section1 -> sectionQ1 - sectionQ2... | Section2 -> section2Q1 - section2Q2 ....
        Question__c[] testQuestions =  new Question__c[]{sec1,qlname,qbday,qdept,sec2,qdesc,qasst};
        Map<String, Schema.SObjectType> AllObjects = Schema.getGlobalDescribe();

        Schema.DescribeSObjectResult pObject = AllObjects.get(OBJ_CONTACT).getDescribe();
        SelectOption[] fields = new SelectOption[]{};
        for(Schema.Sobjectfield f:pObject.fields.getMap().values()){
            fields.add(new SelectOption(f.getDescribe().getName(),f.getDescribe().getLabel()));
        }
        SurveyFieldMappingController controller = new SurveyFieldMappingController( new ApexPages.StandardController(survey1) );

        // Check if the controller have load all the nedded data to create the new mappings.
        System.assertEquals(testQuestions.size(),controller.questions.size());
        for(integer i = 0;i<controller.questions.size();i++){
            system.assertEquals(testQuestions[i].Caption__c, controller.questions[i].question.Caption__c); // Check if the questions in the controller are well ordered
        }
        system.assertEquals(0,controller.headerColumns.size());

        // Create a new HeadColumn(SurveyMapping__c)
        controller.AddHeadColumn();
        system.assertEquals(1,controller.headerColumns.size());

        // Select one object and check if the field selectoption is proper populated for each question and for the mapping field.
        controller.Index = 0;//Select the first Object column
        controller.headerColumns[0].ObjectName = OBJ_CONTACT;
        controller.changeObject();
        system.assertEquals(1, controller.headerColumns.size());
        system.assertEquals(OBJ_CONTACT, controller.headerColumns[0].ObjectName);

        // Checks if the questionMapping__c and surveyQuestion__c records are properly created.
        controller.questions[1].Columns[0].SelectedField = 'LastName'; // Contact last name is required.
        controller.questions[2].Columns[0].SelectedField = 'Birthdate';
        controller.questions[3].Columns[0].SelectedField = 'Department';
        controller.questions[5].Columns[0].SelectedField = 'Description';
        controller.questions[6].Columns[0].SelectedField = ''; // This question is not mapped.
        controller.Save();

        SurveyMapping__c[] surveyMap = [SELECT Id,MatchingField__c,ObjectApiName__c,Survey__c FROM SurveyMapping__c WHERE Survey__c = :survey1.id limit 1];
        QuestionMapping__c[] questionMappings = [SELECT Id,FieldApiName__c,Question__c,SurveyMapping__c FROM QuestionMapping__c WHERE SurveyMapping__c in :SurveyMap];
        System.assertEquals(1,surveyMap.size());
        System.assertEquals(OBJ_CONTACT,surveyMap[0].ObjectApiName__c);

        //Only 4 of the 5 question had a value, the last question was set as None.
        system.assertEquals(4,questionMappings.size());
        Map<Id, String> mappedFields = new Map<Id, String>{
            qbday.Id => 'Birthdate',
            qdept.Id => 'Department',
            qdesc.Id => 'Description'
        };
        for (QuestionMapping__c qm : [SELECT Id,FieldApiName__c,Question__c,SurveyMapping__c
                                        FROM QuestionMapping__c
                                        WHERE SurveyMapping__c in :SurveyMap
                                        AND Question__c in (:qbday.Id, :qdept.Id, :qdesc.Id)]){
            system.assertEquals(mappedFields.get(qm.Question__c), qm.FieldApiName__c);
        }

        // Check the loading process, bring the saved surveyMapping__c and Questionmapping__c records
        SurveyFieldMappingController controller2 = new SurveyFieldMappingController( new ApexPages.StandardController(survey1) );
        system.assertEquals(1,controller2.headerColumns.size());
        system.assertEquals(OBJ_CONTACT,controller2.headerColumns[0].ObjectName);

        //If i save again the SurveyMapping__c record should be updated.
        controller.headerColumns[0].ObjectName = OBJ_ACCOUNT;
        controller.changeObject();
        controller.questions[1].Columns[0].SelectedField = 'Name'; // Account name is required.
        controller.SaveandNext();
        SurveyMapping__c[] updatedSurveyMap = [SELECT Id,MatchingField__c,ObjectApiName__c,Survey__c FROM SurveyMapping__c WHERE Id=:SurveyMap[0].Id limit 1];
        system.assertEquals(OBJ_ACCOUNT,updatedSurveyMap[0].ObjectApiName__c);
        /*
         *TESTS WHEN READONLY MODE IS ACTIVE OR WHEN THE USER HAVE NO PERMISSION TO EDIT THE SURVEY
         */
        Apexpages.currentPage().getParameters().put('b','0');
        controller = new SurveyFieldMappingController( new ApexPages.StandardController(survey1) );
        //ReadOnly should be true
        system.assert(controller.readOnly);
        // Select one object and check if the field selectoption is proper populated for each question and for the mapping field.
        controller.Index = 0;//Select the first Object column
        //Select a new object, contact instead of Account(current obj)
        controller.headerColumns[0].ObjectName = OBJ_CONTACT;
        controller.changeObject();
        system.assertEquals(1, controller.headerColumns.size());
        system.assertEquals(OBJ_CONTACT, controller.headerColumns[0].ObjectName);
        controller.questions[1].Columns[0].SelectedField = ''; // Contact last name is required.
        controller.questions[2].Columns[0].SelectedField = '';
        controller.questions[3].Columns[0].SelectedField = '';
        controller.questions[5].Columns[0].SelectedField = '';
        controller.questions[6].Columns[0].SelectedField = ''; // This question is not mapped.
        controller.Save();
        surveyMap = [SELECT Id,MatchingField__c,ObjectApiName__c,Survey__c FROM SurveyMapping__c WHERE Survey__c = :survey1.id limit 1];
        questionMappings = [SELECT Id,FieldApiName__c,Question__c,SurveyMapping__c FROM QuestionMapping__c WHERE SurveyMapping__c in :SurveyMap];
        //Even though I chose other values it shouldn't be any change due I'm in read only mode
        System.assertNOTEquals(OBJ_CONTACT,surveyMap[0].ObjectApiName__c);
        for (QuestionMapping__c qm : [SELECT Id,FieldApiName__c,Question__c,SurveyMapping__c
                                        FROM QuestionMapping__c
                                        WHERE SurveyMapping__c in :SurveyMap
                                        AND Question__c in (:qbday.Id, :qdept.Id, :qdesc.Id)]){
            system.assertEquals(mappedFields.get(qm.Question__c), qm.FieldApiName__c);
        }
    }
    static testMethod void checkLicense(){
        ISO_Country__c countryCode = new ISO_Country__c(Name='AA',ShortName__c='Test');
        insert countryCode;
        Survey__c survey = new Survey__c(IsPPI__c=true,CountryCode__c='AA',PPIDataYear__c = 2103);
        insert survey;
        //Without permission to edit and create ppi surveys
        LicenseSettings__c testLicense = newLicense(false,'Test');
        insert testLicense;
        try{
            ApexPages.Standardcontroller stdController = new ApexPages.StandardController(survey);
            SurveyFieldMappingController extension = new SurveyFieldMappingController(stdController);
        }catch(Exception e){
            system.assertEquals( system.label.SYSTEM_ERR_NOLICENSEPERMISSION ,e.getMessage());   
        }
        //Without permission to edit and create ppi surveys
        LicenseSettings__c testWithLicense = newLicense(true,'Test1');
        insert testWithLicense;
        ApexPages.Standardcontroller stdController = new ApexPages.StandardController(survey);
        SurveyFieldMappingController extension = new SurveyFieldMappingController(stdController);    
    }

    /**
     * This test the proper throw an error when i try to make changes in the Field mapping for this survey mean while the survey is been update.
     * This should not let me save the changes since the survey was modified.
     */
    static testmethod void testOutofSyncUpdateError(){
        Survey__c newSurvey = new Survey__c(Name='SurveyTest');
        insert newSurvey;
        Question__c sec1 = newQuestion(newSurvey, null, 'Section', 'section1', 'section1', 1);
        insert sec1;
        Question__c auxQuestion = newQuestion (newSurvey,sec1,QuestionType.QUESTION_TEXT_SHORT,'Text1','Text1',1);
        insert auxQuestion;
        SurveyMapping__c sM = new SurveyMapping__c(ObjectApiName__c = 'Contact',Survey__c = newSurvey.Id);
        insert sM;
        QuestionMapping__c qM = new QuestionMapping__c(Question__c = auxQuestion.Id,SurveyMapping__c = sM.Id,FieldApiName__c = 'LastName');
        insert qM;
        test.startTest();
        ApexPages.StandardController stdController = new ApexPages.StandardController(newSurvey);
        SurveyFieldMappingController controller = new SurveyFieldMappingController(stdController);
        //Update the survey directly(this is not using the controller.)
        newSurvey.Alias__c = 'as';
        
        // Wait a second and update the new survey
        C.wait(1);
        update newSurvey;

        newSurvey = [Select LastModifiedDate FROM Survey__c WHERE Id=:newsurvey.Id limit 1];
        DateTime currentLastModifiedDate = newSurvey.LastModifiedDate;
        controller.headerColumns[0].ObjectName = OBJ_CONTACT;
        controller.Questions[1].Columns[0].SelectedField = 'LastName';
        //If i try to save the survey from the controller it shouldnt be posible.
        controller.save();
        //It should trow an error
        System.assert(ApexPages.hasMessages(ApexPages.Severity.FATAL) || ApexPages.hasMessages(ApexPages.Severity.ERROR));
        //And it shouldn't update the survey.
        newSurvey = [Select LastModifiedDate FROM Survey__c WHERE Id=:newsurvey.Id limit 1];
        System.assertEquals(currentLastModifiedDate,newSurvey.LastModifiedDate);
        test.stopTest();
    }

    /**
     * This test if mean while im updating the field mapping no body is updating the survey. So all should be right.
     */
    static testmethod void testOutofSyncUpdateRight(){
        Survey__c newSurvey = new Survey__c(Name='SurveyTest');
        insert newSurvey;
        Question__c sec1 = newQuestion(newSurvey, null, 'Section', 'section1', 'section1', 1);
        insert sec1;
        Question__c auxQuestion = newQuestion (newSurvey,sec1,QuestionType.QUESTION_TEXT_SHORT,'Text1','Text1',1);
        insert auxQuestion;
        SurveyMapping__c sM = new SurveyMapping__c(ObjectApiName__c = 'Contact',Survey__c = newSurvey.Id);
        insert sM;
        QuestionMapping__c qM = new QuestionMapping__c(Question__c = auxQuestion.Id,SurveyMapping__c = sM.Id,FieldApiName__c = 'LastName');
        insert qM;

        test.startTest();
        ApexPages.StandardController stdController = new ApexPages.StandardController(newSurvey);
        SurveyFieldMappingController controller = new SurveyFieldMappingController(stdController);
        newSurvey = [Select LastModifiedDate FROM Survey__c WHERE Id=:newsurvey.Id limit 1];
        DateTime currentLastModifiedDate = newSurvey.LastModifiedDate;
        controller.headerColumns[0].ObjectName = OBJ_CONTACT;
        controller.Questions[1].Columns[0].SelectedField = 'LastName';
        //If i try to save the survey from the controller it should be posible.
        
        // Wait a second and save
        C.wait(1);
        controller.save();

        //It should throw no error
        newSurvey = [Select LastModifiedDate FROM Survey__c WHERE Id=:newsurvey.Id limit 1];
        System.assert(!(ApexPages.hasMessages(ApexPages.Severity.FATAL) || ApexPages.hasMessages(ApexPages.Severity.ERROR)));
        //And it should update the survey.
        newSurvey = [Select LastModifiedDate FROM Survey__c WHERE Id=:newsurvey.Id limit 1];
        System.assertNotEquals(currentLastModifiedDate,newSurvey.LastModifiedDate);
    }

    //----------Utility methods------------
    static Survey__c newSurvey(String surveyName){
        Survey__c s = new Survey__c(Name = surveyName,Status__c = 'Draft');
        insert s;
        return s;
    }
    static Question__c newQuestion(Survey__c survey, Question__c parent, String type, String name, String caption, Integer position){
        return new Question__c(Survey__c = survey.Id, Parent__c = (parent != null ? parent.Id : null), Type__c = type, Name = name, Caption__c = caption, Position__c = position);
    }
    static LicenseSettings__c newLicense(Boolean PPIEditable, String licName){
        list<LicenseSettings__c> lS = [SELECT PPI_Editable__c FROM LicenseSettings__c limit 1];
        delete lS;
        LicenseSettings__c li = new LicenseSettings__c(PPI_Editable__c = PPIEditable, Name = licName);
        return li;
    }
}