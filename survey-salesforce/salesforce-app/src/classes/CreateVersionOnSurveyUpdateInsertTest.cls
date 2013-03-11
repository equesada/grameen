@isTest
private class CreateVersionOnSurveyUpdateInsertTest {

    static testMethod void VersionCreationTest() {
        // Create a new Survey
        Survey__c survey = new Survey__c(Name='survey');
        insert survey;
        //refresh survey
        survey = [SELECT Name,Status__c,Version__c FROM Survey__c WHERE Id = :survey.Id];
        //Check the Version number assignation and Status(before insert)
        system.assertEquals('Draft', survey.Status__c);
        system.assertEquals(1, Survey.Version__c);
        //check if a Survey Version was inserted
        List<SurveyVersion__c> versions = [SELECT Version__c,Status__c,Name FROM SurveyVersion__c WHERE Survey__r.Id = :survey.Id];
        system.assertEquals(1,versions.size());
        system.assertEquals(1, versions[0].Version__c);
        system.assertEquals('Draft',versions[0].Status__c);

        //update surveyVersion to Published,this way the survey will be Published as well
        versions[0].Status__c = 'Published';
        update versions;

        // update a survey from published to draft. It's version number must increase.
        // Also, a new version must be created, and the previous version must remain published
        survey.Status__c = 'Draft';
        update survey;
        system.assertEquals(2, [SELECT Version__c FROM Survey__c WHERE Id = :survey.Id].Version__c);

        versions = [SELECT Version__c,Status__c,Name 
                    FROM SurveyVersion__c
                    WHERE Survey__r.Id = :survey.Id
                    ORDER BY Version__c ASC];

        system.assertEquals(2,versions.size());
        system.assertEquals(1, versions[0].Version__c);
        system.assertEquals('Published',versions[0].Status__c);        
        system.assertEquals(2, versions[1].Version__c);
        system.assertEquals('Draft',versions[1].Status__c);
    }

    // Only the latest version must update the survey's status.
    static testMethod void testOldVersionsDontUpdateSurveyStatus(){
        // Create a survey (sill be draft). Publish it, then change it back to draft (a new version will be created)
        Survey__c survey = new Survey__c(Name='survey');
        insert survey;

        SurveyVersion__c version1 = [SELECT Id, Status__c FROM SurveyVersion__c WHERE Survey__c = :survey.Id];
        version1.Status__c = 'Published';
        update version1;

        survey = [SELECT Id, Status__c FROM Survey__c WHERE Id = :survey.Id];
        survey.Status__c = 'Draft';
        update survey;

        // Assert that initial information is right
        survey = [SELECT Id, Status__c, Version__c FROM Survey__c WHERE Id = :survey.Id];
        system.assertEquals(2, survey.Version__c);
        system.assertEquals('Draft', survey.Status__c);
        
        SurveyVersion__c[] versions = [SELECT Id, Version__c, Status__c FROM SurveyVersion__c WHERE Survey__c = :survey.Id ORDER BY Version__c];
        system.assertEquals(2, versions.size());
        system.assertEquals('Published', versions[0].Status__c);
        system.assertEquals('Draft', versions[1].Status__c);

        // Change the status of the first version. The status of the survey must remain in Draft
        version1 = versions[0];
        version1.Status__c = 'Closed';
        update version1;

        // Assert that final information is right
        survey = [SELECT Id, Status__c, Version__c FROM Survey__c WHERE Id = :survey.Id];
        system.assertEquals(2, survey.Version__c);
        system.assertEquals('Draft', survey.Status__c); 
        
        versions = [SELECT Id, Version__c, Status__c FROM SurveyVersion__c WHERE Survey__c = :survey.Id ORDER BY Version__c];
        system.assertEquals(2, versions.size());
        system.assertEquals('Closed', versions[0].Status__c);
        system.assertEquals('Draft', versions[1].Status__c);
    }
}