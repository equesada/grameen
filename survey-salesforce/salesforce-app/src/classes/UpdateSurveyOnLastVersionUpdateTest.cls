@isTest
private class UpdateSurveyOnLastVersionUpdateTest {

    static testMethod void testSurveyUpdateOnLastVersionUpdate() {

        // Create a new Survey
        Survey__c survey = new Survey__c(Name='survey');
        insert survey;
        //refresh survey
        survey = [select Name,Status__c,Version__c from Survey__c where Id = :survey.Id];
        SurveyVersion__c version = [select Version__c,Status__c,Name from SurveyVersion__c where Survey__r.Id = :survey.Id];
        version.Status__c = 'Published';
        update version;

        //the survey would have to be updated
        //refresh survey
        survey = [select Name,Status__c,Version__c from Survey__c where Id = :survey.Id];
        system.assertEquals('Published', survey.Status__c);
        //get the last version and try to update it from any status to draft,it should fail
        version = [select Status__c from SurveyVersion__c where Survey__c= :survey.Id order by Version__c desc];
        try{
            version.Status__c = 'Draft';
            update version;
            system.assert(false);
        }
        catch (Exception e){
            system.assert(true);
        }

    }

    static testMethod void testSurveyStatusUpdates() {
        Survey__c survey = new Survey__c(Name='survey'); insert survey;

        // Test change status from Draft to Published
        SurveyVersion__c version = [select Version__c,Status__c,Name from SurveyVersion__c where Survey__r.Id = :survey.Id];
        version.Status__c = 'Published'; update version;
        system.assertEquals('Published', [SELECT Status__c FROM Survey__c WHERE Id = :survey.Id].Status__c);

        // Test change status from Published to Closed
        version = [select Version__c,Status__c,Name from SurveyVersion__c where Survey__r.Id = :survey.Id];
        version.Status__c = 'Closed'; update version;
        system.assertEquals('Closed', [SELECT Status__c FROM Survey__c WHERE Id = :survey.Id].Status__c);

        // Test change status from Draft to Closed
        Survey__c closeableSurvey = new Survey__c(Name='survey'); insert closeableSurvey;

        version = [select Version__c,Status__c,Name from SurveyVersion__c where Survey__r.Id = :closeableSurvey.Id];
        version.Status__c = 'Closed'; update version;
        system.assertEquals('Closed', [SELECT Status__c FROM Survey__c WHERE Id = :closeableSurvey.Id].Status__c);
    }
}