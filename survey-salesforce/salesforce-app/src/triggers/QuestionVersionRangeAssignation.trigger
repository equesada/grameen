trigger QuestionVersionRangeAssignation on Question__c (before insert,before update) {


    /*Before insert/update a ScoringGroup, check if the survey is in Draft
    abort the operation and show an error*/
    public final static String ERR_NO_EDITABLE_SURVEY = 'The Survey that contains this Question cannot be edited';
    
    // Get Ids of affected surveys
    Set<Id> surveyIds = new Set<Id>();
    for(Question__c q : trigger.new)
        surveyIds.add(q.Survey__c);

    // Get all 'Draft' surveys
    Map<Id, Survey__c> surveys = new Map<Id, Survey__c>([SELECT Id, Status__c, Version__c FROM Survey__c WHERE Id IN :surveyIds]);

    // Add an error to the scoring groups of 'Draft' surveys
    for(Question__c question:trigger.new){
        if(surveys.get(question.Survey__c).Status__c != 'Draft')
            question.addError(ERR_NO_EDITABLE_SURVEY);
    }

    if(trigger.isInsert){

        // Get questions that were updated
        Set<Id> updatedQuestionsIds = new Set<Id>();
        for(Question__c q : trigger.new) updatedQuestionsIds.add(q.PreviousVersionQuestion__c);
        Map<Id,Question__c> updatedQuestions = new Map<Id,Question__c>([
            SELECT Id,FromVersion__c,ToVersion__c, PreviousVersionQuestion__c
            FROM Question__c
            WHERE Id IN :updatedQuestionsIds]);

        /*Assign the correct version for a question and limit the previous one*/
        //&& updatedQuestions.get(question.PreviousVersionQuestion__c)!= null
        for (Question__c question:trigger.new){
            question.FromVersion__c = surveys.get(question.Survey__c).Version__c;
            Question__c previousQuestion = updatedQuestions.get(question.PreviousVersionQuestion__c);
            if(previousQuestion != null)
                previousQuestion.ToVersion__c = question.FromVersion__c - 1;
        }
        update updatedQuestions.values();
    }

}