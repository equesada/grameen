public with sharing class SurveyUtils{

    // Will clone the specified survey
    public void cloneSurvey(Id surveyToClone, Id newSurveyId, boolean cloneAllAssignments){
        Question__c[] surveyQuestions = [SELECT Parent__c,
                                                Name,
                                                Id,
                                                Survey__c,
                                                Caption__c,
                                                Hint__c,
                                                Maximum__c,
                                                Minimum__c,
                                                DoAfter__c,
                                                DoBefore__c,
                                                Type__c,
                                                Position__c,
                                                Required__c,
                                                CurrentTime__c,
                                                SamePage__c,
                                                SkipLogicBehavior__c,
                                                SkipLogicOperator__c,
                                                Survey__r.Id,
                                                Survey__r.Name,
                                                Survey__r.Alias__c
                                             FROM Question__c
                                             WHERE Survey__c = :surveyToClone
                                             order by Parent__r.Position__c asc, Position__c asc];
        if (surveyQuestions.size() > 0)
            this.cloneQuestions(surveyQuestions, surveyToClone, newSurveyId);
        if (cloneAllAssignments)
            cloneSurveyAssignments(surveyToClone,newSurveyId);
        if([SELECT Id,isPPI__c FROM Survey__c WHERE Id = :surveyToClone].isPPI__c)
            clonePPITables(surveyToClone, newSurveyId);    

    }
    //Method used to clone questions and all their dependant objects
    public void cloneQuestions(Question__c[] questions,Id sourceSurveyId, Id clonedSurveyId){

        //first clone all Questions of type Section
        Map<Id, Question__c> clonedSections = new Map<Id, Question__c>();
        for (Question__c q:questions){
            if(q.Type__c == QuestionType.QUESTION_SECTION) {
                Question__c clonedS = q.clone(false);
                clonedS.Survey__c = clonedSurveyId;
                clonedSections.put(q.Id,clonedS);
            }
        }
        insert clonedSections.values();

        //assign all the questions to their correct parent(section)
        //and prepare them for insert
        Map<Id,Question__c> clonedQuestions = new Map<Id,Question__c>();
        for (Question__c q:questions){
            if(q.Type__c != QuestionType.QUESTION_SECTION) {
                Question__c clonedQ = q.clone(false);
                clonedQ.Position__c = q.Position__c;
                clonedQuestions.put(q.Id,clonedQ);
                clonedQ.Parent__c = (q.Parent__c != null) ? clonedSections.get(q.Parent__c).Id : null;
                clonedQ.Survey__c = clonedSurveyId;
                clonedQuestions.put(q.Id,clonedQ);
            }
        }
        insert clonedQuestions.values();

        // Clone all question options for this survey,
        // changing their references to the cloned questions.
        Option__c[] surveyOptions = [SELECT Question__c, Name, Position__c,Caption__c
                                     FROM   Option__c
                                     WHERE  Question__r.Survey__c = :questions[0].Survey__c];

        Map<Id,Option__c> clonedOptions = this.cloneOptions(surveyOptions, clonedQuestions);
        cloneSkipConditions(clonedQuestions,clonedOptions,sourceSurveyId);
        cloneObjectMapping(clonedQuestions,sourceSurveyId,clonedSurveyId);
        cloneScoring(clonedOptions, sourceSurveyId, clonedSurveyId);
    }

    public Map<Id,Option__c> cloneOptions(Option__c[] options,Map<Id,Question__c> clonedQuestions){
        Map<Id,Option__c> clonedOptions = new Map<Id,Option__c>();
        for (Option__c o:options){
            Option__c clonedO = o.clone(false);
            clonedO.Question__c = clonedQuestions.get(o.Question__c).Id;
            clonedOptions.put(o.Id, clonedO);
        }
        insert clonedOptions.values();
        return clonedOptions;
    }

    /*Method to clone all the skip Conditions of a Survey and relate it with the correct
        cloned Questions*/
    public void cloneSkipConditions(Map<Id, Question__c> clonedQuestions,Map<Id,Option__c> clonedOptions,Id sourceSurveyId){
        List<SkipCondition__c> conditionsSource = [select
                                                    Name,
                                                    Id,Parent__c,
                                                    Parent__r.Name,
                                                    SourceQuestion__r.Name,
                                                    SourceQuestion__r.Type__c,
                                                    SourceQuestion__c,
                                                    Value__c,
                                                    Negate__c,
                                                    Condition__c
                                                    from SkipCondition__c where SourceQuestion__r.Survey__c = :SourceSurveyId];
        List<SkipCondition__c> conditionsToClone= new List<SkipCondition__c>();


        for (SkipCondition__c SkipCondition:ConditionsSource){

            SkipCondition__c clonedSkipCondition = SkipCondition.clone();
            if ((SkipCondition.SourceQuestion__r.Type__c == QuestionType.QUESTION_RADIO || SkipCondition.SourceQuestion__r.Type__c == QuestionType.QUESTION_CHECKBOX)){
                clonedSkipCondition.Value__c = (SkipCondition.Value__c!=null)? clonedOptions.get( Id.valueOf(SkipCondition.Value__c)).Id : null;
            }else {
                clonedSkipCondition.Value__c = SkipCondition.Value__c;
            }
            clonedSkipCondition.SourceQuestion__c =  clonedQuestions.get(Id.valueOf(SkipCondition.SourceQuestion__c)).Id;
            clonedSkipCondition.Parent__c =  clonedQuestions.get(Id.valueOf(SkipCondition.Parent__c)).Id;
            ConditionsToClone.add(clonedSkipCondition);
        }
        insert ConditionsToClone;
    }

    public void cloneScoring(Map<Id, Option__c> clonedOptions, Id sourceSurveyId, Id clonedSurveyId) {
        Map<Id, ScoringGroup__c> clonedGroups = new Map<Id, ScoringGroup__c>();
        list<ScoringGroup__c> clonedGroupsToInsert = new list<ScoringGroup__c>();
        for(ScoringGroup__c sg : [select Id, Survey__c, Caption__c
                                  from ScoringGroup__c
                                  where Survey__c = :sourceSurveyId
                                  order by Id]){
            ScoringGroup__c clonedGroup = sg.clone(false);
            clonedGroup.Survey__c = clonedSurveyId;
            clonedGroupsToInsert.add(clonedGroup);
            clonedGroups.put(sg.Id, clonedGroup);
        }
        insert clonedGroupsToInsert;

        ScoreValue__c[] clonedValues = new ScoreValue__c[]{};
        for(ScoreValue__c sv : [select Id, Option__c, ScoringGroup__c, Value__c
                                from  ScoreValue__c
                                where ScoringGroup__r.Survey__c = :sourceSurveyId
                                order by Id]){
            ScoreValue__c clonedValue = sv.clone(false);
            clonedValue.ScoringGroup__c = clonedGroups.get(sv.ScoringGroup__c).Id;
            clonedValue.Option__c = clonedOptions.get(sv.Option__c).Id;
            clonedValues.add(clonedValue);
        }
        insert clonedValues;
    }

    public void cloneSurveyAssignments(Id sourceSurveyId,Id cloneSurveyId){
        List<ContactGroupSurveyAssignment__c> sourceAssignments = [select Id,ContactGroup__c from ContactGroupSurveyAssignment__c where Survey__c = :sourceSurveyId];
        List<ContactGroupSurveyAssignment__c> clonedAssignments = new List<ContactGroupSurveyAssignment__c>();
        for (ContactGroupSurveyAssignment__c assignment:sourceAssignments){
            clonedAssignments.add(new ContactGroupSurveyAssignment__c(Survey__c=cloneSurveyId,
                                                                         ContactGroup__c=assignment.ContactGroup__c));
        }
        insert clonedAssignments;
    }

    //Clone the Mapping objects of the cloned survey

    public void cloneObjectMapping(Map<Id, Question__c> sourceQuestionIdToClonedQuestion,Id sourceSurvey,Id newSurvey){
        Map<Id,SurveyMapping__c> sourceSurveyMappingToClonedSurveyMapping = new Map<Id,SurveyMapping__c>();
        List<QuestionMapping__c> clonedQuestionMappings = new List<QuestionMapping__c>();
        List<SurveyMapping__c> clonedSurveyMappings = new List<SurveyMapping__c>();
        for (SurveyMapping__c sm:[select 	MatchingField__c,
                                            ObjectApiName__c,
                                            Survey__c,
                                            Id from SurveyMapping__c
                                            where Survey__c=:sourceSurvey]){
            SurveyMapping__c clonedSurveyMapping = new SurveyMapping__c(MatchingField__c = sm.MatchingField__c,
                                                                        ObjectApiName__c = sm.ObjectApiName__c,
                                                                        Survey__c = newSurvey
                                                                        );
            clonedSurveyMappings.add(clonedSurveyMapping);
            SourceSurveyMappingToClonedSurveyMapping.put(sm.Id,clonedSurveyMapping);
        }
        insert clonedSurveyMappings;
        for (QuestionMapping__c qm:[select 	FieldApiName__c,
                                            Question__c,
                                            SurveyMapping__c,
                                            Id from QuestionMapping__c
                                            where SurveyMapping__r.Survey__c=:sourceSurvey]){
            clonedQuestionMappings.add( new QuestionMapping__c(FieldApiName__c = qm.FieldApiName__c,
            Question__c = sourceQuestionIdToClonedQuestion.get(qm.Question__c).Id,
            SurveyMapping__c = SourceSurveyMappingToClonedSurveyMapping.get(qm.SurveyMapping__c).Id));
        }
        insert clonedQuestionMappings;

    }
    private void clonePPITables(Id sourceSurveyId, Id clonedSurveyId){
        Map<Id,PPITableDataSet__c> clonedTables = new Map<Id,PPITableDataSet__c>();
        Map<Id,PPITableLine__c> clonedTableLines = new Map<Id,PPITableLine__c>();
        
        List<PPITableDataSet__c> pPITableD = [SELECT Id, PPITable__r.Name, PPITable__r.Id FROM PPITableDataSet__c WHERE Survey__c = :sourceSurveyId];
        List<PPITableLine__c> pPILines = [SELECT Id, PPITableDataSet__r.Id, From__c, To__c, Probability__c FROM PPITableLine__c WHERE PPITableDataSet__r.Survey__c = :sourceSurveyId];        
        
        for(PPITableDataSet__c pTable:pPITableD){
            PPITableDataSet__c clonedTable = pTable.clone(false);
            clonedTable.PPITable__c = pTable.PPITable__c;
            clonedTable.Survey__c = clonedSurveyId;
            clonedTables.put(pTable.Id, clonedTable);                
        }
        insert clonedTables.values();
        
        for(PPITableLine__c pTableLine:pPILines){
            PPITableLine__c clonedTableLine = pTableLine.clone(false);
            clonedTableLine.PPITableDataSet__c = clonedTables.get(pTableLine.PPITableDataSet__c).Id;
            clonedTableLine.Probability__c = pTableLine.Probability__c;
            clonedTableLine.To__c = pTableLine.To__c;
            clonedTableLine.From__c = pTableLine.From__c;
            clonedTableLines.put(pTableLine.Id, clonedTableLine);
        }
        insert clonedTableLines.values();
    }
}