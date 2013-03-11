@isTest
public class SurveyPreviewControllerTest{

  // Test for preview controller
  static testMethod void testPreview(){

      //Set data
      Survey__c survey1 = newSurvey('FirstSurvey');
      Question__c question1 = newQuestion(survey1.Id, 'Name', 'Caption1', 1);
      Question__c question2 = newQuestion(survey1.Id, 'Name2', 'Caption2', 2);
      Option__c option1 = newOption(question1.Id, 'NameOption', 1);
      SkipCondition__c skipCondition1 = newSkipCondition(question1.Id, question2.Id, 'val1', 'Is');
      ScoringGroup__c scoringGroup1 = newScoringGroup(survey1.Id, 'captionScoring');
      ScoreValue__c scoreValue1 = newScoreValue(scoringGroup1.Id, option1.Id, 7);

      ApexPages.StandardController stdController = new ApexPages.StandardController(survey1);
      SurveyPreviewController controller = new SurveyPreviewController(stdController);
      PageReference pg = new PageReference('/apex/SurveyPreview');
      Test.setCurrentPage(pg);
      String previousPage = 'https://' + ApexPages.currentPage().getHeaders().get('Host') + '/apex/SurveyManager';
      ApexPages.currentPage().getHeaders().put('Referer', previousPage );

      controller.setDataForPreview();
      system.assertEquals( false, controller.fromNewSurvey );

      String serializedQuestions = controller.getQuestions();
      String expectedString = '[{"type":"Section","skipLogicOperator":"All","skipLogicBehavior":"Show","skipconditions"' +
                              ':[{"value":"val1","sourceQuestionId":"' + question2.Id + '","negate":false,"id":"' + skipCondition1.Id +
                              '","condition":"Is"}],"scoreValues":null,"samePage":false,"required":false,"qhint":null,"position":1,"parent":null,"options":' +
                              '[{"scoreValues":[{"value":7,"ScoringGroupId":"' + scoringGroup1.Id + '","optionId":"' + option1.Id +
                              '","id":"' + scoreValue1.Id  + '"}],"position":1,"name":"NameOption","id":"' + option1.Id + '","caption":"NameOption"}]' +
                              ',"name":"Name","minimum":null,"maximum":null,"id":"' + question1.Id +
                              '","doBefore":null,"doAfter":null,"deletedOptions":[],"currentTime":false,"caption":"Caption1"}' +
                              ',{"type":"Section","skipLogicOperator":"All","skipLogicBehavior":"Show","skipconditions":[],'+
                              '"scoreValues":null,"samePage":false,"required":false,"qhint":null,"position":2,"parent":null,"options":[],"name":"Name2","minimum":null,' +
                              '"maximum":null,"id":"' + question2.Id + '","doBefore":null,"doAfter":null,"deletedOptions":[],' +
                              '"currentTime":false,"caption":"Caption2"}]';

      system.assertEquals( expectedString  , serializedQuestions  );
  }
  //----------Utility methods------------

    static Survey__c newSurvey(String surveyName){
        Survey__c sur = new Survey__c(Status__c = 'Draft', Name = surveyName);
        insert sur;
        return sur;
    }
    static Question__c newQuestion(Id surveyId, String name, String caption, Integer position){
        Question__c question = new Question__c(Name = name, Survey__c = surveyId, Caption__c = caption, Position__c = position);
        insert question;
        return question;
    }
    static Option__c newOption(Id questionId, String name, Integer position){
        Option__c option = new Option__c(Name = name, Caption__c = name, Position__c = position, Question__c = questionId);
        insert option;
        return option;
    }

    static SkipCondition__c newSkipCondition(Id questionParent, Id questionSrc, String val, String condition){
        SkipCondition__c sc = new SkipCondition__c(Parent__c = questionParent, SourceQuestion__c = questionSrc, Value__c = val, Condition__c = condition);
        insert sc;
        return sc;
    }

    static ScoringGroup__c newScoringGroup(Id surveyId, String caption){
        ScoringGroup__c sg = new ScoringGroup__c (Survey__c = surveyId, Caption__c = caption);
        insert sg;
        return sg;
    }

    static ScoreValue__c newScoreValue( Id scoreGroupId, Id optionScoreId, Integer value ){
        ScoreValue__c sv = new ScoreValue__c (ScoringGroup__c = scoreGroupId, Option__c = optionScoreId, Value__c = value);
        insert sv;
        return sv;
    }
}