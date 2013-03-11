 /*
*	 Trigger for Insert SurveyVersions when creating new surveys,
* 	or updating surveys,also managethe Version numbers
*
*/
trigger CreateVersionOnSurveyUpdateInsert on Survey__c (after insert,before insert, before update) {

    List<SurveyVersion__c> surveyVersions = new List<SurveyVersion__c>();
    //before insertion assign the survey Version and Status
    if (trigger.isBefore && trigger.isInsert){
        for (Survey__c sur:trigger.new){
            sur.Version__c=1;
            sur.Status__c='Draft';
        }
    //if a new survey is inserted,create a version for it after insert the survey
    }else if (trigger.isAfter && trigger.isInsert){
        for (Survey__c sur:trigger.new){
            surveyVersions.add(new SurveyVersion__c(Survey__c = sur.Id,Status__c='Draft',Version__c=1));
        }
    /**	if a Survey is been updated and its status it's being changed from 'Published' to 'Draft',
        create a new Version.
        *To create a new Version an Update over Survey__c must be executed*
      **/
    }else{
        for (Survey__c sur:trigger.new){
            if( trigger.oldMap.get(sur.Id).Status__c!='Draft' && trigger.newMap.get(sur.Id).Status__c=='Draft' ){
                sur.Version__c ++;
                surveyVersions.add(new SurveyVersion__c(Survey__c = sur.Id,Status__c='Draft',Version__c=sur.Version__c));
            }
        }
    }
    insert surveyVersions;

}