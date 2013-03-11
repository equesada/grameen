@isTest
private class SurveyQuestionLogicControllerTest {

//------------
// Utility
//------------


    // creates a new survey, and an instance of the controller
    // sets the current page for testing with the needed parameter
    public static SurveyQuestionLogicController getNewController(){

        Survey__c newSurvey = new Survey__c(Status__c = 'Draft'); insert newSurvey;
        Question__c section = new Question__c(Survey__c = newSurvey.Id, Caption__c = 'section', Name = 'section');insert section;
        getQuestionsList(newSurvey.Id, section.Id,'Q');
        ApexPages.StandardController stdController = new ApexPages.StandardController(newSurvey);

        PageReference currentPage = Page.SurveyQuestionLogic;
        Test.setCurrentPage(currentPage);
        currentPage.getParameters().put('id', newSurvey.Id);

        return new SurveyQuestionLogicController(stdController);
    }

  // creates a list of 5 new questions and inserts them
  // also creates options and conditions for them
  public static list<Question__c> getQuestionsList(string surveyId, string parentId,string prefix){

    list<Question__c> listReturn = new list<Question__c>();
    Question__c auxQuestion;
    for (integer i = 0; i < 5; i++){
      auxQuestion = new Question__c (Survey__c = surveyId, Caption__c = prefix + i, Name = prefix + i, Parent__c = parentId,Type__c = 'Text', Position__c = i+1);
      listReturn.add(auxQuestion);
    }
    insert listReturn;

    Option__c auxOption;
    list<Option__c> listOptions = new list<Option__c>();
    SkipCondition__c auxCondition;
    list<SkipCondition__c> listConditions = new list<SkipCondition__c>();
    for (integer i = 0; i < 5; i++){
      auxOption = new Option__c (Question__c = listReturn[i].Id, Name = prefix + i, Position__c = i+1);
      listOptions.add(auxOption);
      if (i != 0){
        auxCondition = new SkipCondition__c (Parent__c = listReturn[i].Id, SourceQuestion__c = listReturn[0].Id, Condition__c = 'Is', Negate__c = false, Value__c = 'Test');
        listConditions.add(auxCondition);
      }
    }
    insert listOptions;
    insert listConditions;

    return listReturn;
  }


//------------
// Test methods
//------------

    static testMethod void saveTest(){
        LicenseSettings__c testLicense = newLicense(false,'Test');
        insert testLicense;
        
        SurveyQuestionLogicController controller = getNewController();
    
        // test with values
        Question__c firstQuestion = [SELECT Id, Caption__c, Name, Type__c FROM Question__c][0];
        Question__c theQuestion = [SELECT Id, Caption__c, Name, Type__c FROM Question__c][2];
        string newQuestion = '[{'
            +'"id":"' + theQuestion.Id + '",'
            +'"caption":"' + theQuestion.Caption__c + '",'
            +'"type":"' + theQuestion.Type__c + '",'
            +'"options":[{"id":"a","name":"a-orig","position":"1"},{"id":"b","name":"b","position":"2"}],'
            +'"skipconditions":[{"id":"123","sourceQuestionId":"' + firstQuestion.Id + '","condition":"Is","value":"Test","negate":true},{"id":"456","sourceQuestionId":"' + firstQuestion.Id + '","condition":"Is","value":"OtherTest","negate":true}]'
            +'}]';

        PageReference currentPage = Page.SurveyQuestionLogic;
        currentPage.getParameters().put('questions', newQuestion);
        currentPage.getParameters().put('quicksave', 'true');
        Test.setCurrentPage(currentPage);

        system.assertEquals(null, controller.save());
        System.assert(!ApexPages.hasMessages(ApexPages.Severity.FATAL) && !ApexPages.hasMessages(ApexPages.Severity.ERROR));


        // test with non-draft survey
        Survey__c theSurvey = [SELECT id, Status__c FROM Survey__c];
        theSurvey.Status__c = 'Published';
        update theSurvey;
        controller.save();
        System.assert(ApexPages.hasMessages(ApexPages.Severity.FATAL) || ApexPages.hasMessages(ApexPages.Severity.ERROR));
    }

    static testMethod void checkLicense(){
        ISO_Country__c countryCode = new ISO_Country__c(Name='AA',ShortName__c='Test');
        insert countryCode;
        Survey__c survey = new Survey__c(IsPPI__c=true,CountryCode__c='AA',PPIDataYear__c = 2103);
        insert survey;
        LicenseSettings__c testLicense = newLicense(false,'Test');
        insert testLicense;
        try{
            ApexPages.Standardcontroller stdController = new ApexPages.StandardController(survey);
            SurveyQuestionLogicController extension = new SurveyQuestionLogicController(stdController);
            extension.survey.IsPPI__c=true;
        }catch(Exception e){
            system.assertEquals( system.label.SYSTEM_ERR_NOLICENSEPERMISSION, e.getMessage());   
        }
    }

    static LicenseSettings__c newLicense(Boolean PPIEditable, String licName){
        list<LicenseSettings__c> lS = [SELECT PPI_Editable__c FROM LicenseSettings__c limit 1];
        delete lS;
        LicenseSettings__c li = new LicenseSettings__c(PPI_Editable__c = PPIEditable, Name = licName);
        return li;
    }

    /**
     * This test the proper throw an error when I try to make changes in the Field mapping 
     * for this survey mean while the survey is been update.
     * This should not let me save the changes since the survey was modified.
     */
    static testMethod void testOutofSyncSaveError(){

        // PREPARE: Create a base survey that will be edited
        Survey__c survey = new Survey__c(Name='survey');
        insert survey;
        Question__c section = new Question__c(Survey__c=survey.Id,Name='section',Caption__c='section',Position__c=1,Type__c=QuestionType.QUESTION_SECTION);
        insert section;
        Question__c sourceq = new Question__c(Survey__c=survey.Id,Name='source',Parent__c=section.Id, Caption__c='source',Position__c=1,Type__c=QuestionType.QUESTION_TEXT_SHORT);
        Question__c targetq = new Question__c(Survey__c=survey.Id,Name='target',Parent__c=section.Id, Caption__c='target',Position__c=2,Type__c=QuestionType.QUESTION_TEXT_SHORT);
        insert new Question__c[]{sourceq,targetq};

        // TEST: Have someone else edit the survey, and try to edit the skip condition
        string newQuestion = '[{'
            +'"id":"' + section.Id + '",'
            +'"caption":"' + section.Caption__c + '",'
            +'"type":"' + section.Type__c + '",'
            +'"skipconditions":[]'
            +'},{'
            +'"id":"' + sourceq.Id + '",'
            +'"caption":"' + sourceq.Caption__c + '",'
            +'"type":"' + sourceq.Type__c + '",'
            +'"skipconditions":[]'
            +'},{'
            +'"id":"' + targetq.Id + '",'
            +'"caption":"' + targetq.Caption__c + '",'
            +'"type":"' + targetq.Type__c + '",'
            +'"skipconditions":['
                +'{'
                +'"id":"qwerty",'
                +'"sourceQuestionId":"' + sourceq.Id + '",'
                +'"condition":"' + QuestionType.SKIPLOGIC_CONDITION_ISANSWERED + '",'
                +'"value":"Test",'
                +'"negate":true}]'
            +'}]';

        PageReference currentPage = Page.SurveyQuestionLogic;
        currentPage.getParameters().put('id', survey.Id);
        Test.setCurrentPage(currentPage);
        SurveyQuestionLogicController controller = new SurveyQuestionLogicController(new ApexPages.StandardController(survey));

        Test.startTest();
        // Edit the survey outside the controller
        C.wait(1);
        survey = [SELECT Id, Alias__c FROM Survey__c WHERE Id = :survey.Id];
        survey.Alias__c = 'Changed';
        update survey;

        currentPage.getParameters().put('questions', newQuestion);
        currentPage.getParameters().put('quicksave', 'true');
        controller.save();
        Test.stopTest();

        // ASSERT: check that the controller was unable to save
        System.assert(ApexPages.hasMessages(ApexPages.Severity.FATAL) || ApexPages.hasMessages(ApexPages.Severity.ERROR));
    }
}