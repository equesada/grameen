@isTest
public class SurveyQuestionControllerTest{
    // Helper method to test empty strings
    private static Boolean empty(String s){
        return s == null || s.length() == 0;
    }

    //------------
    // Stub class for easy accessing controller methods
    //------------
    private class ControllerStub {
        public SurveyQuestionController controller;
        public String error = '';
        public String firstSection;
        public ControllerStub(boolean withTestData){
            Survey__c newSurvey = new Survey__c(Name = 'survey', Status__c = 'Draft'); insert newSurvey;

            LicenseSettings__c license = C.getLicense();
            license.PPI_Editable__c = false;
            update license;

            if (withTestData){
                Question__c auxQuestion = new Question__c (Caption__c = 'Test1', Name = 'Test1', Type__c = 'Text', Survey__c = newSurvey.Id); insert auxQuestion;
                SkipCondition__c auxCondition = new SkipCondition__c (Parent__c = auxQuestion.Id, SourceQuestion__c = auxQuestion.Id, Condition__c = 'Is', Value__c = 'Test'); insert auxCondition;
                Option__c auxOption = new Option__c (Question__c = auxQuestion.Id, Name = 'TestOption', Position__c = 1); insert auxOption;
                ScoringGroup__c auxScoringGroup = new ScoringGroup__c (Survey__c = newSurvey.Id, Caption__c = 'TestGroup'); insert auxScoringGroup;
                ScoreValue__c auxScoreValue = new ScoreValue__c (ScoringGroup__c = auxScoringGroup.Id, Option__c = auxOption.Id, Value__c = 10); insert auxScoreValue;
            }
            ApexPages.StandardController stdController = new ApexPages.StandardController(newSurvey);
            this.controller = new SurveyQuestionController(stdController);
        }

        public void addQ(String question){
            ApexPages.currentPage().getParameters().put('question', question);
            controller.addQuestionController();
            this.error = controller.error;
        }

        public void editQ(String question){
            ApexPages.currentPage().getParameters().put('question', question);
            controller.editQuestionController();
            this.error = controller.error;
        }

        public void removeQ(String name){
            ApexPages.currentPage().getParameters().put('qId', name);
            controller.removeQuestionController();
            this.error = controller.error;
        }
        public void moveQ(String originId, String destinationId){
            ApexPages.currentPage().getParameters().put('originId',originId);
            ApexPages.currentPage().getParameters().put('destinationId',destinationId);
            controller.moveQuestionController();
            this.error = controller.error;
            this.firstSection = SurveyQuestionController.FIRST_SECTION ;

        }

        public void publish(){
            controller.survey.Status__c='Published';
            update controller.survey;
        }

        public BuilderQuestion getQ(String id){ return controller.findQuestion(id); }
    }

    //------------
    // Test methods
    //------------

    static testMethod void addQuestion(){

        ControllerStub stub = new ControllerStub(false);

        // add a typeless question (should fail)
        stub.addQ('{"id":"Question1","caption":"This is question one."}');
        system.assert(!empty(stub.error), 'Should have complained that type is missing.');

        // add a single question
        stub.addQ('{'
            +'"id":"a",'
            +'"caption":"This is question one.",'
            +'"type":"text",'
            +'"options":[{"name":"a","position":"1"},{"name":"b","position":"2"}]'
            +'}');
        system.assertEquals('This is question one.',stub.controller.questions[0].caption);
        system.assertEquals(2, stub.controller.questions[0].options.size());
        system.assertEquals('a', stub.controller.questions[0].options[0].name);
    }

    static testMethod void editQuestion(){
        ControllerStub stub = new ControllerStub(false);

        // edits a non-existing question (should fail)
        stub.editQ('{"id":"nonexistent","caption":"a","name":"a","type":"text"}');
        system.assert(!empty(stub.error), 'Should have complained that question does not exist');

        // edit existing question: change description, keep caption
        stub.addQ('{"id":"a","caption":"a","name":"a","qhint":"a","type":"text"}');
        stub.editQ('{"id":"a","caption":"a","name":"a","qhint":"a-edited","type":"text"}');
        BuilderQuestion q = stub.getQ('a');
        system.assert(empty(stub.error), stub.error);
        system.assertEquals('a-edited', q.qhint);
        system.assertEquals('text', q.type);

        // edit a question, and have it named like another question
        stub.addQ('{"id":"b","caption":"b","name":"b","qhint":"a","type":"text"}');
        stub.editQ('{"id":"b","caption":"a","name":"a","qhint":"a","type":"text"}');
        system.assert(!empty(stub.error), 'Should have complained that caption is repeated');
    }

    static testMethod void removeQuestion(){

        ControllerStub stub = new ControllerStub(false);

        // remove a non-existing question (should fail)
        stub.removeQ('nonexistent');
        system.assert(!empty(stub.error), 'Should have complained that question does not exist');

        // remove an existing question (other questions should be shifted)
        stub.addQ('{"id":"a","caption":"a","name":"a","type":"text"}');
        stub.addQ('{"id":"b","caption":"b","name":"b","type":"text"}');
        system.assertEquals(2, stub.controller.questions.size());
        stub.removeQ('a');
        system.assert(empty(stub.error), stub.error);
        system.assertEquals(1, stub.controller.questions.size());
    }

    static testMethod void save(){
        //Cleaning from original questions
        List<Question__c> q = [SELECT Id FROM Question__c WHERE Name='Test1'];
        delete q;
        ControllerStub stub = new ControllerStub(false);

        // Prepare some questions to save
        stub.addQ('{"id":"a","caption":"a","name":"a","type":"text","position":1}');
        stub.addQ('{"id":"b","caption":"b","name":"b","type":"text","position":2}');

        // Save and check only one question exists
        stub.controller.quickSave();
        system.assertEquals(2, stub.controller.questions.size());

        Question__c[] savedQuestions = [select Id, Name from Question__c where Survey__c = :stub.controller.survey.Id];
        system.assertEquals(2, savedQuestions.size());

        // Remove a question, assert it's on the deletedQuestions list
        // Save and assert the question was removed from the database
        stub.removeQ('b');
        system.assertEquals(1, stub.controller.questions.size());
        system.assertEquals('b', stub.controller.deletedQuestions[0].id);

        stub.controller.quickSave();

        savedQuestions = [select Id, Caption__c from Question__c where Survey__c = :stub.controller.survey.Id];
        system.assertEquals(1, savedQuestions.size());
        system.assertEquals('a', savedQuestions[0].Caption__c);

        BuilderQuestion a = stub.controller.findQuestion('a');
        system.assertEquals(savedQuestions[0].Caption__c, a.caption);

        // Add a question and remove it before saving,
        // assert that it's not on the questions or deleted questions list
        stub.addQ('{"id":"c","caption":"c","name":"c","type":"text","position":2}');
        stub.removeQ('c');
        system.assertEquals(null, stub.controller.findQuestion('c'));
        system.assertNotEquals(null, stub.controller.findQuestion('a'));

        // testing page with skip conditions
        stub = new ControllerStub(true);

        Question__c auxQuestion = [SELECT Id FROM Question__c WHERE Name = 'Test1'];

        string param ='[{"id":"' + auxQuestion.Id + '","caption":"Test1","name":"Test1","type":"Text", "skipconditions":[{"id":"c", "sourceQuestionId":"' + auxQuestion.Id + '", "condition":"Is", "value":"Test", "negate":false}]}]';
        ApexPages.currentPage().getParameters().put('questions', param);

        stub.controller.quickSave();
    }

    static testMethod void questionOptions(){
        ControllerStub stub = new ControllerStub(false);
        stub.addQ('{'
            +'"id":"q",'
            +'"caption":"questionOptions()",'
            +'"type":"text",'
            +'"options":[{"id":"a","name":"a-orig","position":"1"},{"id":"b","name":"b","position":"2"}]'
            +'}');
        stub.editQ('{'
            +'"id":"q",'
            +'"caption":"questionOptions()",'
            +'"type":"text",'
            +'"options":[{"id":"a","name":"a","position":"1"},{"id":"c","name":"c","position":"2"}]'
            +'}');

        BuilderQuestion q = stub.getQ('q');
        system.assertEquals(2, q.options.size());
        system.assertEquals('a', q.getOption('a').name);

        stub.controller.quickSave();
        Id qid = [Select Id from Question__c where Caption__c = 'questionOptions()'][0].Id;
        Option__c[] ops = [select Id, Name from Option__c where Question__c = :qid order by Id];
        system.assertEquals(2, ops.size());
        system.assertEquals('a', ops[0].Name);
        system.assertEquals('c', ops[1].Name);

        // Let's try deleting c and adding d
        stub.editQ('{'
            +'"id":"q",'
            +'"caption":"This is still question one.",'
            +'"type":"text",'
            +'"options":[{"id":"a","name":"a","position":"1"},{"id":"d","name":"d","position":"2"}]'
            +'}');
        system.assertEquals(2, q.options.size());
        system.assertEquals(1, q.deletedOptions.size()); // c was saved before, so it must be flagged to delete
        system.assert(q.deletedOptions.contains('c'), 'Question "' + ops[1] + '" not contained in deleted options: ' + q.deletedOptions);
        stub.controller.quickSave();
        ops = [select Id, Name from Option__c where Question__c = :qid order by Position__c];
        system.assertEquals(2, ops.size());
        system.assertEquals('a', ops[0].Name);
        system.assertEquals('d', ops[1].Name);
    }

    static testMethod void sections(){
        ControllerStub stub = new ControllerStub(false);

        stub.addQ('{"id":"s","caption":"Section 1","name":"Section_1","type":"section","position":"1"}');
        stub.addQ('{"id":"a","caption":"a","name":"a","type":"text","parent":"s","position":"1"}');
        stub.addQ('{"id":"b","caption":"b","name":"b","type":"text","parent":"s","position":"2"}');
        stub.controller.save();

        Question__c[] savedQuestions = [select Id, Name, Parent__c from Question__c where Survey__c = :stub.controller.survey.Id order by Caption__c];
        system.assertEquals(3, savedQuestions.size());
        Question__c a = savedQuestions[0];
        Question__c b = savedQuestions[1];
        Question__c s = savedQuestions[2];
        system.assertEquals(a.Parent__c, s.Id);
        system.assertEquals(b.Parent__c, s.Id);

        stub.addQ('{"id":"t","caption":"Section 2","name":"Section_2","type":"section"}');
        stub.addQ('{"id":"c","caption":"c","name":"c","type":"text","parent":"t"}');
        stub.addQ('{"id":"d","caption":"d","name":"d","type":"text","parent":"t"}');
        stub.controller.removeQuestion('s');

        savedQuestions = [select Id, Name, Parent__c from Question__c where Survey__c = :stub.controller.survey.Id order by Caption__c];
        system.assertEquals(3, savedQuestions.size());
        Question__c c = savedQuestions[0];
        Question__c d = savedQuestions[1];
        Question__c t = savedQuestions[2];
        system.assertEquals(c.Parent__c, t.Id);
        system.assertEquals(d.Parent__c, t.Id);
    }

    static testMethod void editSurvey(){
        ControllerStub stub = new ControllerStub(false);
        stub.addQ('{'
            +'"id":"q",'
            +'"caption":"questionOptions()",'
            +'"type":"text",'
            +'"options":[{"id":"a","name":"a-orig","position":"1"},{"id":"b","name":"b","position":"2"}]'
            +'}');
        stub.controller.quickSave();

        ApexPages.StandardController stdController = new ApexPages.StandardController(stub.controller.survey);
        SurveyQuestionController controller = new SurveyQuestionController(stdController);
        system.assertEquals(1, controller.questions.size());
        BuilderQuestion bq = controller.questions[0];
        system.assertEquals(2, bq.options.size());
    }
    static testMethod void move(){
        ControllerStub stub = new ControllerStub(false);
        //prepare some questions to move
        //Sections
        stub.addQ('{"id":"sec1","caption":"first","name":"first_section","type":"section","position":1}');
        stub.addQ('{"id":"sec2","caption":"second","name":"second_section","type":"section","position":2}');
        //Questions
        stub.addQ('{"id":"a","caption":"first","name":"first","type":"text","position":1,"parent":"sec1"}');
        stub.addQ('{"id":"b","caption":"second","name":"second","type":"text","position":2,"parent":"sec1"}');
        stub.addQ('{"id":"c","caption":"third","name":"third","type":"text","position":3,"parent":"sec1"}');

        BuilderQuestion q1 = stub.getQ('a');
        BuilderQuestion q2 = stub.getQ('b');
        BuilderQuestion q3 = stub.getQ('c');

        //----SAME SECTION------
        //Move down
        stub.moveQ('a','b');
        system.assertEquals(2,q1.position);
        system.assertEquals(1,q2.position);
        //Move up
        stub.moveQ('c','b');
        system.assertEquals(1,q2.position);
        system.assertEquals(2,q3.position);
        system.assertEquals(3,q1.position);

        //----DIFFERENT SECTIONS-----
        stub.removeQ('b');
        system.assertEquals(1,q3.position);
        system.assertEquals(2,q1.position);
        stub.addQ('{"id":"b","caption":"second","name":"second","type":"text","position":1,"parent":"sec2"}');

        q2 = stub.getQ('b');
        BuilderQuestion sec1 = stub.getQ('sec1');
        BuilderQuestion sec2 = stub.getQ('sec2');
        //Move down
        stub.moveQ('a','b');
        system.assertEquals(2,q1.position);
        system.assertEquals('sec2',q1.parent);
        system.assertEquals(1,q2.position);
        system.assertEquals('sec2',q2.parent);
        system.assertEquals(1,q3.position);
        system.assertEquals('sec1',q3.parent);
        //Move up
        stub.moveQ('b','c');
        system.assertEquals(1,q1.position);
        system.assertEquals('sec2',q1.parent);
        system.assertEquals(2,q2.position);
        system.assertEquals('sec1',q2.parent);
        system.assertEquals(1,q3.position);
        system.assertEquals('sec1',q3.parent);

        //-------INSERT FIRST--------
        //-------SAME SECTION--------
        stub.moveQ('b','sec1');
        system.assertEquals(1,q2.position);
        system.assertEquals('sec1',q2.parent);
        system.assertEquals(1,q1.position);
        system.assertEquals('sec2',q1.parent);
        system.assertEquals(2,q3.position);
        system.assertEquals('sec1',q3.parent);

        //----DIFFERENT SECTIONS-------
        stub.moveQ('b','sec2');
        system.assertEquals(1,q2.position);
        system.assertEquals('sec2',q2.parent);
        system.assertEquals(2,q1.position);
        system.assertEquals('sec2',q1.parent);
        system.assertEquals(1,q3.position);
        system.assertEquals('sec1',q3.parent);
        //---------------------------------

        //-------MOVE SECTIONS---------
        stub.moveQ('sec1','sec2');
        system.assertEquals(2,sec1.position);
        system.assertEquals(1,sec2.position);
        system.assertEquals('sec1',q3.parent);
        system.assertEquals('sec2',q2.parent);
        system.assertEquals('sec2',q1.parent);
        //-----MOVE SECTION FIRST PLACE-----
        stub.moveQ('sec1', SurveyQuestionController.FIRST_SECTION);
        system.assertEquals(1,sec1.position);
        system.assertEquals(2,sec2.position);

    }

    static testmethod void checkNotPublishAlreadyPublished(){
        //PREPARING THE TEST
        ControllerStub stub = new ControllerStub(false);
        stub.publish();
        //PERFORMING THE TEST
        stub.controller.save();
        PageReference pageReference = stub.controller.saveContinue();

        //CHECKING IF A COULD SAVE IT
        System.assertEquals(SurveyQuestionController.ERR_SURVEY_ALREADY_PUBLISHED, stub.controller.error);

        //CHECKING THE SAVE AND CONTINUE, I HAVE TO STAY IN THIS PAGE
        System.assertEquals(null,pageReference);
        //PREPARING THE TEST
        stub = new ControllerStub(false);
        //PERFORMING THE TEST
        stub.controller.save();
        pageReference = stub.controller.saveContinue();

        //CHECKING IF A COULD SAVE IT
        System.assert(stub.controller.error.length() == 0);

        //CHECKING THE SAVE AND CONTINUE, I HAVE TO STAY IN THIS PAGE
        System.assertNotEquals(null,pageReference);
    }
    /**
     * This test the proper throw an error when i try to make changes in the Field mapping for this survey mean while the survey is been update.
     * This should not let me save the changes since the survey was modified.
     */
    static testmethod void testOutofSyncUpdateError(){

        Survey__c newSurvey = new Survey__c(Name='SurveyTest');
        insert newSurvey;
        Question__c section = new Question__c (Caption__c = 'sec1', Name = 'sec1', Type__c = QuestionType.QUESTION_SECTION, Survey__c = newSurvey.Id,Position__c = 1);
        insert section;
        Question__c auxQuestion = new Question__c (Caption__c = 'Test1', Name = 'Test1', Type__c = QuestionType.QUESTION_TEXT_SHORT, Survey__c = newSurvey.Id,Position__c =1,Parent__c = section.Id);
        insert auxQuestion;
        test.startTest();
        ApexPages.StandardController stdController = new ApexPages.StandardController(newSurvey);
        SurveyQuestionController controller = new SurveyQuestionController(stdController);
        //Update the survey directly(this is not using the controller.)
        newSurvey.Alias__c = 'as';

        C.wait(1);
        update newSurvey;
        newSurvey = [Select LastModifiedDate FROM Survey__c WHERE Id=:newsurvey.Id limit 1];
        DateTime currentLastModifiedDate = newSurvey.LastModifiedDate;
        //If i try to save the survey from the controller it shouldnt be posible.
        controller.save();
        //It should trow an error
        System.assertNotEquals(0, controller.error.length(), 'Errors expected, but no error was found.');
        //And it shouldn't update the survey.
        newSurvey = [Select LastModifiedDate FROM Survey__c WHERE Id=:newsurvey.Id limit 1];
        System.assertEquals(currentLastModifiedDate,newSurvey.LastModifiedDate);

        //Reload the controller now the last modified date will be updated.
        controller = new SurveyQuestionController(stdController);
        C.wait(1);
        controller.save();

        //It should trow no error, and it should update the survey
        System.assertEquals(0, controller.error.length(), 'Found errors, but none were expected.');
        newSurvey = [Select LastModifiedDate FROM Survey__c WHERE Id=:newsurvey.Id limit 1];
        System.assertNotEquals(currentLastModifiedDate,newSurvey.LastModifiedDate);
        test.stopTest();
    }
    static testMethod void checkLicense(){
        ISO_Country__c countryCode = new ISO_Country__c(Name='AA',ShortName__c='Test');
        insert countryCode;
        Survey__c survey = new Survey__c(IsPPI__c=true,CountryCode__c='AA',PPIDataYear__c = 2103);
        insert survey;

        LicenseSettings__c license = C.getLicense();
        license.PPI_Editable__c = false;
        update license;

        try{
            ApexPages.Standardcontroller stdController = new ApexPages.StandardController(survey);
            SurveyQuestionController extension = new SurveyQuestionController(stdController);
            extension.init();
        }catch(Exception e){
            system.assertEquals(system.label.SYSTEM_ERR_NOLICENSEPERMISSION, e.getMessage());
        }

        license = C.getLicense();
        license.PPI_Editable__c = true;
        update license;

        ApexPages.Standardcontroller stdController = new ApexPages.StandardController(survey);
        SurveyQuestionController extension = new SurveyQuestionController(stdController);
        extension.init();
    }

    static testMethod void testDeleteMappedQuestion_Required(){
        Survey__c survey = newSurvey('survey');
        Question__c section = newQuestion(survey, null, QuestionType.QUESTION_SECTION, 's', 's', 1); insert section;
        Question__c question = newQuestion(survey,section,QuestionType.QUESTION_TEXT_SHORT,'q','q',1); insert question;


        SurveyMapping__c smapping = new SurveyMapping__c(Survey__c=survey.Id,ObjectApiName__c='Contact');
        insert smapping;
        QuestionMapping__c qmapping = new QuestionMapping__c(SurveyMapping__c=smapping.Id,Question__c=question.Id,FieldApiName__c='LastName');
        insert qmapping;


        //try to delete required question, it shouldn't be possible.
        Test.startTest();
            SurveyQuestionController controller = new SurveyQuestionController(new ApexPages.StandardController(survey));
            ApexPages.currentPage().getParameters().put('qId',question.Id);
        controller.removeQuestionController();
        Test.stopTest();
        //It should throw an error message.
        system.assertEquals(1, [SELECT Id FROM Question__c WHERE Id=:question.Id].size());
        system.assert(controller.error != null && controller.error != '', 'Expected error on page, but none found');
    }

    static testMethod void testDeleteMappedQuestion_NotRequired(){
        Survey__c survey = newSurvey('survey');
        Question__c section = newQuestion(survey, null, QuestionType.QUESTION_SECTION, 's', 's', 1); insert section;
        Question__c question = newQuestion(survey,section,QuestionType.QUESTION_TEXT_SHORT,'q','q',1); insert question;

        // Map the question to a required field
        SurveyMapping__c smapping = new SurveyMapping__c(Survey__c=survey.Id,ObjectApiName__c='Contact');
        insert smapping;
        QuestionMapping__c qmapping = new QuestionMapping__c(SurveyMapping__c=smapping.Id,Question__c=question.Id,FieldApiName__c='Description');
        insert qmapping;

        //I should not remove the question at save.
        Test.startTest();
            SurveyQuestionController controller = new SurveyQuestionController(new ApexPages.StandardController(survey));
            ApexPages.currentPage().getParameters().put('qId',question.Id);
            controller.removeQuestionController();
            system.assert(controller.error == null || controller.error == '', 'Found error, but none was expected');
        controller.save();
        Test.stopTest();

        //If I delete the mapping first, I should be able to delete the question.
        system.assert([SELECT Id FROM Question__c WHERE Id = :question.Id].isEmpty());
        system.assert([SELECT Id FROM QuestionMapping__c WHERE Id = :qmapping.Id].isEmpty());
    }
        //Delete a question without mapping
    static testMethod void testDeleteMappedQuestion_NotMapped(){
        Survey__c survey = newSurvey('survey');
        Question__c section = newQuestion(survey, null, QuestionType.QUESTION_SECTION, 's', 's', 1); insert section;
        Question__c question = newQuestion(survey,section,QuestionType.QUESTION_TEXT_SHORT,'q','q',1); insert question;
        //Delete a question with not required mapping
        Test.startTest();
            SurveyQuestionController controller = new SurveyQuestionController(new ApexPages.StandardController(survey));
            ApexPages.currentPage().getParameters().put('qId',question.Id);
        controller.removeQuestionController();
            system.assert(controller.error == null || controller.error == '', 'Found error, but none was expected');
        controller.save();
        Test.stopTest();
        //Delete a question with required default mapping
        system.assert([SELECT Id FROM Question__c WHERE Id = :question.Id].isEmpty());
    }
    static testMethod void testDeleteMappedQuestion_MappedWithDefaultValue(){
        // TO BE DONE: There are no standard fields that are required and have default value at the same time.
        system.assert(true);
    }

    //Question Order Test
    static testMethod void initQuestionOrder(){
        Survey__c sur = newSurvey('surveyTest');
        Question__c sec1 = newQuestion(sur, null, QuestionType.QUESTION_SECTION, 'sectionTest', 'sectionTest',1);
        insert sec1;
        Question__c quest1 = newQuestion(sur, sec1, QuestionType.QUESTION_TEXT_SHORT, 'question1', 'question1',1);
        Question__c quest2 = newQuestion(sur, sec1, QuestionType.QUESTION_TEXT_SHORT, 'question2', 'question2',2);
        Question__c quest3 = newQuestion(sur, sec1, QuestionType.QUESTION_TEXT_SHORT, 'question3', 'question3',3);
        insert new Question__c[]{quest1, quest2, quest3};
        
        //Question Order-----
        quest3.Position__c = 2;
        update quest3;
        
        ApexPages.StandardController stdController = new ApexPages.StandardController(sur);
        SurveyQuestionController controller = new SurveyQuestionController(stdController);
        controller.init();
        quest3 =[select Id, Position__c from Question__c where Id = :quest3.Id];
        //Position fixed
        system.assertNotEquals( quest3.Position__c, quest2.Position__c ); 
        system.assertEquals(3,quest3.Position__c);
        system.assertEquals(2,quest2.Position__c);
        system.assertEquals(1,quest1.Position__c);
        //-------------------
    }
    static testMethod void initSectionOrder(){        
        Survey__c sur = newSurvey('surveyTest');
        Question__c sec1 = newQuestion(sur, null, QuestionType.QUESTION_SECTION, 'sectionTest', 'sectionTest',1);
        insert sec1;
        
        //Section order------
        Question__c sec2 = newQuestion(sur, null, QuestionType.QUESTION_SECTION, 'sectionTest2', 'sectionTest2',2);
        insert sec2;
        
        sec2.Position__c=1;
        update sec2;
        
        ApexPages.StandardController stdController2 = new ApexPages.StandardController(sur);
        SurveyQuestionController controller2 = new SurveyQuestionController(stdController2);
        controller2.init();
        sec2=[select Id, Position__c from Question__c where Id = :sec2.Id];
        //Position fixed
        system.assertNotEquals( sec2.Position__c, sec1.Position__c ); 
        system.assertEquals(1,sec1.Position__c);
        system.assertEquals(2,sec2.Position__c);
        //--------------------
    }
    static testMethod void initSectionWrongOrder(){
        Survey__c sur = newSurvey('surveyTest');
        Question__c sec1 = newQuestion(sur, null, QuestionType.QUESTION_SECTION, 'sectionTest', 'sectionTest',1);
        insert sec1;
        Question__c sec2 = newQuestion(sur, null, QuestionType.QUESTION_SECTION, 'sectionTest2', 'sectionTest2',2);
        insert sec2;
        
        sec1.Position__c = 210;
        update sec1;
        
        ApexPages.StandardController stdController = new ApexPages.StandardController(sur);
        SurveyQuestionController controller = new SurveyQuestionController(stdController);
        controller.init();
        sec1 = [select Id, Position__c from Question__c where Id = :sec1.Id];
        sec2 = [select Id, Position__c from Question__c where Id = :sec2.Id];
        
        //Position fixed
        system.assertEquals(sec1.Position__c, sec2.Position__c+1);
        system.assertEquals(2,sec1.Position__c);
        system.assertEquals(1,sec2.Position__c);
        //--------------------------- 
        
    }
    static testMethod void initQuestWrongOrder(){
        Survey__c sur = newSurvey('surveyTest');
        Question__c sec1 = newQuestion(sur, null, QuestionType.QUESTION_SECTION, 'sectionTest', 'sectionTest',1);
        insert sec1;
        Question__c quest3 = newQuestion(sur, sec1, QuestionType.QUESTION_TEXT_SHORT, 'question3', 'question3',1);
        insert quest3;
        
        //Wrong position--------------
        Question__c quest4 = newQuestion(sur, sec1, QuestionType.QUESTION_TEXT_SHORT, 'question1', 'question1',2);
        insert quest4;
        
        quest4.Position__c = 210;
        update quest4;
        
        ApexPages.StandardController stdController3 = new ApexPages.StandardController(sur);
        SurveyQuestionController controller3 = new SurveyQuestionController(stdController3);
        controller3.init();
        quest4 = [select Id, Position__c from Question__c where Id = :quest4.Id];
        
        //Position fixed
        system.assertEquals(quest4.Position__c, quest3.Position__c+1);
        system.assertEquals(2,quest4.Position__c);
        //--------------------------- 
        
    }

    // Test that skipped positions get fixed
    static testMethod void testInitFirstQuestionIsNotPositionOne(){
        Survey__c sur = newSurvey('surveyTest');
        Question__c sec1 = newQuestion(sur, null, QuestionType.QUESTION_SECTION, 'sectionTest', 'sectionTest',1);
        insert sec1;
        Question__c quest1 = newQuestion(sur, sec1, QuestionType.QUESTION_TEXT_SHORT, 'question1', 'question1',4);
        Question__c quest2 = newQuestion(sur, sec1, QuestionType.QUESTION_TEXT_SHORT, 'question2', 'question2',8);
        insert new Question__c[]{quest1, quest2};
        
        ApexPages.StandardController stdController = new ApexPages.StandardController(sur);
        SurveyQuestionController controller = new SurveyQuestionController(stdController);
        controller.init();

        // Check positions were fixed
        quest1 = [select Id, Position__c from Question__c where Id = :quest1.Id];
        quest2 = [select Id, Position__c from Question__c where Id = :quest2.Id];
        system.assertEquals(1, quest1.Position__c);
        system.assertEquals(2, quest2.Position__c);
    }
    //----------Utility methods------------
    static Survey__c newSurvey(String surveyName){
        Survey__c s = new Survey__c(Name = surveyName, Status__c = 'Draft');
        insert s;
        return s;
    }

    static Question__c newQuestion(Survey__c survey, Question__c parent, String type, String name, String caption, Integer position){
        return new Question__c(Survey__c = survey.Id, Parent__c = (parent != null ? parent.Id : null), Type__c = type, Name = name, Caption__c = caption, Position__c = position);
    }
}