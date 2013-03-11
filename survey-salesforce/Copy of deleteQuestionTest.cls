@isTest
public class deleteQuestionTest {
	static final string IS_CONDITION = 'Is';
    static testMethod void testDeleteQuestionsWithMapping() {
        Survey__c survey1 = newSurvey('FirstSurvey');
        Question__c sec1 = newQuestion(survey1, null,QuestionType.QUESTION_SECTION, 'section1', 'section1', 1);
        Question__c questionWithRequiredMapping = newQuestion(survey1,sec1,QuestionType.QUESTION_TEXT_SHORT,'questionWithRequiredMapping','Last name',1);
        Question__c questionWithoutMapping = newQuestion(survey1,sec1,QuestionType.QUESTION_TEXT_SHORT,'questionWithoutMapping','questionWithoutMapping',2);
        Question__c questionWithNotRequiredMapping = newQuestion(survey1,sec1,QuestionType.QUESTION_TEXT_SHORT,'questionWithNotRequiredMapping','Description',3);
        SurveyMapping__c sM = new SurveyMapping__c(ObjectApiName__c = 'Contact',Survey__c = survey1.Id);
        insert sM;
        QuestionMapping__c qM = new QuestionMapping__c(Question__c = questionWithRequiredMapping.Id,SurveyMapping__c = sM.Id,FieldApiName__c = 'LastName');
        insert qM;
        QuestionMapping__c qM2 = new QuestionMapping__c(Question__c = questionWithNotRequiredMapping.Id,SurveyMapping__c = sM.Id,FieldApiName__c = 'Description');
        insert qM2;
        //try to delete the question, it shouldnt be possible.
        try{
            delete questionWithRequiredMapping;
            System.Assert(false);
        }catch(Exception ex){
            System.Assert(true);
        }
        //If I delete the mapping first, I should be able to delete the question.
        delete sM;
        System.Assert(Database.delete(questionWithRequiredMapping).isSuccess());
        //Delete a question without mapping
        System.Assert(Database.delete(questionWithoutMapping).isSuccess());
        //Delete a question with not required mapping
        System.Assert(Database.delete(questionWithNotRequiredMapping).isSuccess());
    }
    //Test if all the others records related to the deleted question are also deleted.
    static testMethod void relatedRecordsTest() {
        Survey__c survey1 = newSurvey('FirstSurvey');
        Question__c sec1 = newQuestion(survey1, null, 'Section', 'section1', 'section1', 1);
        Question__c quest2 = newQuestion(survey1,sec1,'text-short','quest2','quest2',1);
        Question__c qlname = newQuestion(survey1,sec1,'text-short','lname','Last name',2);
        Question__c quest = newQuestion(survey1,sec1,'text-short','quest','quest',3);
        SkipCondition__c cond1 = new SkipCondition__c(Condition__c = IS_CONDITION,Parent__c = qlname.Id,SourceQuestion__c = quest.Id, Value__c = 'Value');
        insert cond1;
        SkipCondition__c cond2 = new SkipCondition__c(Condition__c = IS_CONDITION,Parent__c = quest2.id,SourceQuestion__c = qlname.id, Value__c = 'Value');
        insert cond2;
        ScoringGroup__c sg = new ScoringGroup__c (Caption__c = 'caption', Survey__c = survey1.id);
        insert sg;
        Option__c option1 = new Option__c(Name = 'NameOption', Position__c = 1, Question__c = qlname.Id);
        insert option1;
        ScoreValue__c sv = new ScoreValue__c(Value__c = 40, ScoringGroup__c = sg.id, Option__c = option1.id);
        insert sv;
        delete qlname;
        //All records related to the question should have been eliminated.
        System.AssertEquals(0,[SELECT Id FROM Option__c WHERE Question__c=: qlname.Id].size());
        System.AssertEquals(0,[SELECT Id FROM ScoreValue__c WHERE Option__c=: option1.Id].size());
        System.AssertEquals(0,[SELECT Id FROM SkipCondition__c WHERE Parent__c=: qlname.Id or SourceQuestion__c=: qlname.Id].size());
        System.AssertEquals(0,[SELECT Id FROM SkipCondition__c WHERE Id=:cond1.Id].size());
        System.AssertEquals(0,[SELECT Id FROM SkipCondition__c WHERE Id=:cond2.Id].size());
    }
    //----------Utility methods------------
    static Survey__c newSurvey(String surveyName){
        Survey__c s = new Survey__c(Name = surveyName);
        insert s;
        return s;
    }
    static Question__c newQuestion(Survey__c survey, Question__c parent, String type, String name, String caption, Integer position){
        Question__c q = new Question__c(Survey__c = survey.Id, Parent__c = (parent != null ? parent.Id : null), Type__c = type, Name = name, Caption__c = caption, Position__c = position);
        insert q;
        return q;
    }
}