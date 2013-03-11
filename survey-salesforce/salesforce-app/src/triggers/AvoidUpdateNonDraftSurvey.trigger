/**
*   Before insert/update a ScoringGroup check if the survey is in Draft, *
   in that case abort the operation and show an error.
*   @author Ernesto Quesada
*/
trigger AvoidUpdateNonDraftSurvey on ScoringGroup__c (before insert, before update) {

    /*Before insert/update a ScoringGroup, check if the survey is in Draft
    abort the operation and show an error*/
    public final static String ERR_NO_EDITABLE_SURVEY = 'The Survey that contains this Question cannot be edited';
      // Get all 'Draft' surveys
    Map<Id, Survey__c> draftSurveys = new Map<Id, Survey__c>([SELECT Id FROM Survey__c WHERE Status__c='Draft']);

    // Add an error to the scoring groups of 'Draft' surveys
    for(ScoringGroup__c sg:trigger.new){
        if(!draftSurveys.containsKey(sg.Survey__c))
            sg.addError(ERR_NO_EDITABLE_SURVEY);
    }
}
