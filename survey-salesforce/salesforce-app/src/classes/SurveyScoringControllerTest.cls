@isTest
public class SurveyScoringControllerTest{

    // Test for Scoring controller
    static testMethod void testScoring(){

        Survey__c survey1 = newSurvey('FirstSurvey');
        Question__c question1 = newQuestion(survey1.Id, 'Name', 'Caption1');
        Option__c option1 = newOption(question1.Id, 'NameOption', 1);
        setPPILicenseSetting(false);
        
        ApexPages.StandardController stdController = new ApexPages.StandardController(survey1);
        SurveyScoringController controller = new SurveyScoringController(stdController);
        String questionString = controller.getQuestions();
        String lGroupsString = controller.getListGroups();
        String lScoresValues = controller.getListScoreValues();

        controller.scoringGroupStr = '[{"caption":"ScoringGroup1"}]';
        // The following json is an array that includes one object(ScoreValue), with the following attributes: option.Id, GroupScore name, and Value.
        // Given this data I'll try to insert it in the database.
        controller.scoringValuesJson  = '[{"optionId":"' + option1.Id + '","groupName":"ScoringGroup1","value":"1"}]';
        controller.continueAfterwards = 'true';
        controller.saveScoring();
        ScoringGroup__c myGroup = [select Id, Caption__c from ScoringGroup__c where Caption__c = 'ScoringGroup1'][0];
        ScoreValue__c myScoreValue = [select Id, Value__c, ScoringGroup__c from ScoreValue__c where ScoringGroup__c = :myGroup.Id][0];
        // After this I'll compare the harcoded value (from the json array) with the retrieved value.
        system.assertEquals(1, myScoreValue.Value__c);

        // Fails because the name of the scoring value has no related group name.
        controller.scoringGroupStr = '[{"caption":"ScoringGroup2"}]';
        controller.scoringValuesJson  = '[{"optionId":"' + option1.Id + '","groupName":"ScoringGroup1","value":"1"}]';
        controller.saveScoring();
        // Returns 2 error messages.
        system.assertEquals(2, Apexpages.getMessages().size());

        // Fails because the next survey is not 'Draft';
        survey1.Status__c = 'Closed';
        update survey1;
        controller.saveScoring();
        // Returns 1 more error message.
        system.assertEquals(3, Apexpages.getMessages().size());

        // Fails because group has no valid name.
        survey1.Status__c = 'Draft';
        update survey1;
        controller = new SurveyScoringController(stdController);
        controller.scoringGroupStr = '[{"caption":""}]';
        controller.scoringValuesJson  = '[{"optionId":"' + option1.Id + '","groupName":"ScoringGroup4","value":"-1"}]';
        controller.saveScoring();
        // Returns 2 more error messages.
        system.assertEquals(5, Apexpages.getMessages().size());
    }

    // Test for Render Scoring
    static testMethod void testRendering(){
        Survey__c survey1 = newSurvey('FirstSurvey');
        Question__c question1 = newQuestion(survey1.Id, 'Name', 'Caption1');
        Option__c option1 = newOption(question1.Id, 'NameOption', 1);
        ScoringGroup__c group1 = newScoringGroup('FirstGroup', survey1.Id);
        ScoreValue__c scoreValue1 = newScoreValue(5, group1.Id, option1.Id);
        setPPILicenseSetting(false);
        
        ApexPages.StandardController stdController = new ApexPages.StandardController(survey1);
        SurveyScoringController controller = new SurveyScoringController(stdController);
        system.assertEquals(1, controller.listScoreValues.size());
    }

    static testMethod void checkLicense(){
        ISO_Country__c countryCode = new ISO_Country__c(Name='AA',ShortName__c='Test');
        insert countryCode;
        Survey__c survey = new Survey__c(IsPPI__c=true,CountryCode__c='AA',PPIDataYear__c = 2103, Status__c = 'Draft');
        insert survey;
        setPPILicenseSetting(false);

        try{
            ApexPages.StandardController stdController = new ApexPages.StandardController(survey);
            SurveyScoringController extension = new SurveyScoringController(stdController);
        }catch(Exception e){
            system.assertEquals( system.label.SYSTEM_ERR_NOLICENSEPERMISSION, e.getMessage());   
        }
    }
    /**
     * This test the proper throw an error when i try to make changes in the Field mapping for this survey mean while the survey is been update.
     * This should not let me save the changes since the survey was modified.
     */
    static testmethod void testOutofSyncUpdateError(){
        Survey__c survey1 = newSurvey('FirstSurvey');
        Question__c question1 = newQuestion(survey1.Id, 'Name', 'Caption1');
        Option__c option1 = newOption(question1.Id, 'NameOption', 1);
        ScoringGroup__c group1 = newScoringGroup('FirstGroup', survey1.Id);
        ScoreValue__c scoreValue1 = newScoreValue(5, group1.Id, option1.Id);
        test.startTest();
        ApexPages.StandardController stdController = new ApexPages.StandardController(survey1);
        SurveyScoringController controller = new SurveyScoringController(stdController);

        //Update the survey directly(this is not using the controller.)
        survey1.Alias__c = 'as';

        C.wait(1);
        update survey1;
        survey1 = [Select LastModifiedDate FROM Survey__c WHERE Id=:survey1.Id limit 1];
        DateTime currentLastModifiedDate = survey1.LastModifiedDate;
        //If i try to save the survey from the controller it shouldnt be posible.
        controller.saveScoring();
        //It should trow an error
        System.assert(ApexPages.hasMessages(ApexPages.Severity.FATAL) || ApexPages.hasMessages(ApexPages.Severity.ERROR));
        System.AssertEquals(system.label.BUILDER_ERR_CONCURRENTSAVE,ApexPages.getMessages()[0].getDetail());
        //And it shouldn't update the survey.
        survey1 = [Select LastModifiedDate FROM Survey__c WHERE Id=:survey1.Id limit 1];
        System.assertEquals(currentLastModifiedDate,survey1.LastModifiedDate);
        test.stopTest();
    }

    /**
     * This test if mean while im updating the field mapping no body is updating the survey. So all should be right.
     */
    static testmethod void testOutofSyncUpdateRight(){
        Survey__c survey = newSurvey('FirstSurvey');
        Question__c question1 = newQuestion(survey.Id, 'Name', 'Caption1');
        Option__c option1 = newOption(question1.Id, 'NameOption', 1);
        ScoringGroup__c group1 = newScoringGroup('FirstGroup', survey.Id);
        ScoreValue__c scoreValue1 = newScoreValue(5, group1.Id, option1.Id);
        test.startTest();
        ApexPages.StandardController stdController = new ApexPages.StandardController(survey);
        SurveyScoringController controller = new SurveyScoringController(stdController);
        // The following json is an array that includes one object(ScoreValue), with the following attributes: option.Id, GroupScore name, and Value.
        // Given this data I'll try to insert it in the database.
        controller.scoringGroupStr = '[{"caption":"ScoringGroup1"}]';
        controller.scoringValuesJson  = '[{"optionId":"' + option1.Id + '","groupName":"ScoringGroup1","value":"1"}]';
        controller.continueAfterwards = 'true';

        C.wait(1);
        survey = [Select LastModifiedDate FROM Survey__c WHERE Id=:survey.Id limit 1];
        DateTime currentLastModifiedDate = survey.LastModifiedDate;
        //If i try to save the survey from the controller it should be posible.
        controller.saveScoring();
        
        //It should throw no error
        System.assert(!(ApexPages.hasMessages(ApexPages.Severity.FATAL) || ApexPages.hasMessages(ApexPages.Severity.ERROR)));
        
        //And it should update the survey.
        survey = [SELECT LastModifiedDate FROM Survey__c WHERE Id = :survey.Id];
        System.assertNotEquals(currentLastModifiedDate,survey.LastModifiedDate);
        test.stopTest();
    }
      //----------Utility methods------------

    private static void setPPILicenseSetting(Boolean value){
        LicenseSettings__c l = C.getLicense();
        l.PPI_Editable__c = value;
        update l;
    }

    static Survey__c newSurvey(String surveyName){
        Survey__c v = new Survey__c(Status__c = 'Draft', Name = surveyName);
        insert v;
        return v;
    }
    static Question__c newQuestion(Id surveyId, String name, String caption){
        Question__c q = new Question__c(Name = name, Survey__c = surveyId, Caption__c = caption);
        insert q;
        return q;
    }
    static Option__c newOption(Id questionId, String name, Integer position){
        Option__c o = new Option__c(Name = name, Position__c = position, Question__c = questionId);
        insert o;
        return o;
    }
    static ScoringGroup__c newScoringGroup(String caption, Id surveyId){
        ScoringGroup__c sg = new ScoringGroup__c (Caption__c = caption, Survey__c = surveyId);
        insert sg;
        return sg;
    }
    static ScoreValue__c newScoreValue(Decimal val, Id groupId, Id optionId){
        ScoreValue__c sv = new ScoreValue__c(Value__c = val, ScoringGroup__c = groupId, Option__c = optionId);
        insert sv;
        return sv;
    }
}