trigger UpdateSurveyOnLastVersionUpdate on SurveyVersion__c (before update) {

    /*trigger to update the Survey__c Status when the last version is updated,and prevent the updating of a
    published survey to Draft*/
    public final static String  ERR_CANNOT_GO_BACK_TO_DRAFT = 'A version can\'t be back to Draft after it has been published.';

    Set<Id> surveysId = new Set<Id>();
    for (SurveyVersion__c SurveyVersion:trigger.new){
        surveysId.add(SurveyVersion.Survey__c);
    }
    Map<Id,Survey__c> idToSurvey = new Map<Id,Survey__c>([
        SELECT Id, Version__c, Status__c
        FROM Survey__c
        WHERE Id IN :surveysId]);

    for(SurveyVersion__c newVersion:trigger.new){
        // Update the survey status with the version status, but only if it's the latest version of the survey.
        // However, a survey cannot go back to Draft from any other status. Raise an error in this situation.
        SurveyVersion__c oldVersion = trigger.oldMap.get(newVersion.Id);
        if (oldVersion.Status__c != 'Draft' && newVersion.Status__c == 'Draft'){
            newVersion.Status__c.AddError(ERR_CANNOT_GO_BACK_TO_DRAFT);
        } else if (idToSurvey.get(newVersion.Survey__c).Version__c == newVersion.Version__c){
            idToSurvey.get(newVersion.Survey__c).Status__c = newVersion.Status__c;
            if(newVersion.Status__c=='Published'){
                 idToSurvey.get(newVersion.Survey__c).PublishedDate__c = Date.today();
            }
        }
    }
    update idToSurvey.values();
}