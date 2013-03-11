/**
*   This is the test class for the CleanXForm trigger
*   @author Santiago Blankleider 
*/
@isTest
private class SetPPISurveyNameTest {
    
    static testMethod void TestSetSurveyName() {
        // needed objects to test
        // AA is an ISO User-assigned code element. See http://www.iso.org/iso/customizing_iso_3166-1.htm
        ISO_Country__c country = new ISO_Country__c(Name = 'AA', ShortName__c = 'Test Country Name');
        insert country;
        Survey__c survey = new Survey__c(CountryCode__c = 'AA', PPIDataYear__c = 2010, IsPPI__c = true);
        insert survey;

        // name should be saved as 'Test Country Name PPI 2010'
        System.assertEquals('Test Country Name PPI 2010', [select Name from Survey__c where Id = :survey.Id].Name);
        Survey__c repeatedSurvey = new Survey__c(CountryCode__c = 'AA', PPIDataYear__c = 2010, IsPPI__c = true);
        try{
            insert repeatedSurvey;
            system.assert(false,'Should have failed here since exitst another survey with same name');
        }catch(Exception e){
            system.assert(e.getMessage().contains('There is already a PPI Survey for this Country and Year. Please select another country or year.'));
        }
    }
    static testMethod void triggerQuestionsTest() {
        Survey__c sur = new Survey__c();
        insert sur;
        List<Question__c> quests = new List<Question__c>();
        Question__c section1 =  newQuestion(1,'section1',null,sur.Id);
        insert section1;
        Question__c question1 = newQuestion(1,'question1',section1.Id,sur.Id);
        quests.add(question1);
        Question__c question2 = newQuestion(2,'question2',section1.Id,sur.Id);
        quests.add(question2);
        Question__c question3 = newQuestion(3,'question3',section1.Id,sur.Id);
        quests.add(question3);
        insert quests;
        
        //Making a position repeated
        question2.Position__c = 1;
        update question2; 
        try{
            update sur;
            system.assert(false, 'Should have failed here since exists two questions with same position');
        }catch(Exception e){
            system.assert(e.getMessage().contains( 'Question question2 Id = '+ question2.Id + ' and Quesiton question1 Id = ' + question1.Id + ' Share parent Id= ' + section1.Id + ' and Position 1'));
        }
        //Reseting question2 order
        question2.Position__c = 2;
        update question2;
        
        //Inserting a new queston with same position than existing one
        Question__c question4 = newQuestion(2,'question4',section1.Id,sur.Id);
        try{
            insert question4;
            system.assert(false, 'Should have failed here since exist another question with same position in this section');
        }catch(Exception e){
            system.assert(true);
        }
    }
     static testMethod void triggerSectionsTest() {
        Survey__c sur = new Survey__c();
        insert sur;
        List<Question__c> quests = new List<Question__c>();
        Question__c section1 =  newQuestion(1,'section1',null,sur.Id);
        insert section1;
        Question__c section2 =  newQuestion(2,'section2',null,sur.Id);
        insert section2;
        
        //Making a position repeated
        section2.Position__c = 1;
        update section2; 
        try{
            update sur;
            system.assert(false, 'Should have failed here since exists two sections with same position');
        }catch(Exception e){
            system.assert(e.getMessage().contains( 'Question section2 Id = '+ section2.Id + ' and Quesiton section1 Id = ' + section1.Id + ' Share parent Id= ' + null + ' and Position 1'));
        }
        //Reseting question2 order
        section2.Position__c = 2;
        update section2;
        //Inserting a new section with same position than existing one
        Question__c section3 = newQuestion(2,'section3',null,sur.Id);
        try{
            insert section3;
            system.assert(false, 'Should have failed here since exist another section with same position ');
        }catch(Exception e){
            system.assert(true);
        }
    }
    //Utility function
    static Question__c newQuestion(Decimal pos, String name, Id parent, Id survey){
        Question__c q = new Question__c(Name = name, Position__c = pos, Parent__c = parent, Survey__c = survey);
        return q;
    }
}