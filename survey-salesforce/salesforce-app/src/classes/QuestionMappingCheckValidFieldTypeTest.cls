@isTest
private class QuestionMappingCheckValidFieldTypeTest {

    static testMethod void InsertUpdateTest() {
        Survey__c survey = new Survey__c(Name='The Survey');
        insert survey;
        Question__c qSection = new Question__c(Survey__c=survey.Id,Caption__c= 'SECTION',Type__c = QuestionType.QUESTION_SECTION);
        insert qSection;
        Integer pos = 1;
        Question__c q1 = new Question__c(Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'Username', Position__c = pos++, Type__c = QuestionType.QUESTION_TEXT_SHORT);
        Question__c q2 = new Question__c(Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'Last Name', Position__c = pos++, Type__c = QuestionType.QUESTION_TEXT_SHORT);
        Question__c qContactDateTime = new Question__c(Name = 'dateTimeQuestion',Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'Datetime', Position__c = pos++, Type__c = QuestionType.QUESTION_TEXT_SHORT);
        Question__c q3 = new Question__c(Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'select some', Position__c = pos++, Type__c = QuestionType.QUESTION_TEXT_SHORT);
        Question__c qLongText = new Question__c(Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'long text', Position__c = pos++, Type__c = QuestionType.QUESTION_TEXT_LONG);
        Question__c qTotalOpportunityQuantity2 = new Question__c(Name = 'OppName',Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'Opportunity Name', Position__c = pos++, Type__c = QuestionType.QUESTION_DATE);
        Question__c qOpCloseDate = new Question__c(Name = 'OppCloseDate',Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'Opportunity Date', Position__c = pos++, Type__c = QuestionType.QUESTION_NUMBER);
        Question__c qTotalOpportunityQuantity = new Question__c(Name = 'OppQuantity',Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'Opportunity Number', Position__c = pos++, Type__c = QuestionType.QUESTION_DATETIME);
        Question__c qOpStageName = new Question__c(Name = 'OppStageName',Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'Opportunity Picklist', Position__c = pos++, Type__c = QuestionType.QUESTION_NUMBER);
        Question__c qOpProbability = new Question__c(Name = 'OppProbability',Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'Opportunity Percent', Position__c = pos++, Type__c = QuestionType.QUESTION_CHECKBOX);
        Question__c qOpIsPrivate = new Question__c(Name = 'OppIsPrivate',Parent__c= qSection.Id,survey__c=survey.Id,Caption__c= 'Opportunity bool', Position__c = pos++, Type__c = QuestionType.QUESTION_RADIO);

        List<Question__c> questions= new List<Question__c>{qLongText,qTotalOpportunityQuantity2,q1,q2,q3,qTotalOpportunityQuantity,qOpStageName,qOpCloseDate,qOpProbability,qContactDateTime,qOpIsPrivate};
        insert questions;

        //Survey Mapping,new Contact
        SurveyMapping__c sm1 = new SurveyMapping__c(Survey__c=survey.Id,ObjectApiName__c='Contact');
        SurveyMapping__c sm1clone = new SurveyMapping__c(Survey__c=survey.Id,ObjectApiName__c='Contact');
        SurveyMapping__c sm2clone = new SurveyMapping__c(Survey__c=survey.Id,ObjectApiName__c='Contact');
        //Survey Mapping with Matching field(Account already in DB)
        SurveyMapping__c sm2 = new SurveyMapping__c(Survey__c=survey.Id,ObjectApiName__c='Account',MatchingField__c='AccountNumber');
        //Survey Mapping opportunities
        SurveyMapping__c smOpportunity = new SurveyMapping__c(Survey__c=survey.Id,ObjectApiName__c='Opportunity',MatchingField__c='Name');
        insert new SurveyMapping__c[]{sm1,sm2,sm1clone,sm2clone,smOpportunity};
            //Question mapping for the contact
        QuestionMapping__c qm1 = new QuestionMapping__c(FieldApiName__c='LastName',Question__c=q2.Id,SurveyMapping__c=sm1.Id);

        QuestionMapping__c qm1clone = new QuestionMapping__c(FieldApiName__c='LastName',Question__c=q1.Id,SurveyMapping__c=sm1clone.Id);
        QuestionMapping__c qm2clone = new QuestionMapping__c(FieldApiName__c='LastName',Question__c=q1.Id,SurveyMapping__c=sm2clone.Id);
        QuestionMapping__c qm1duplicate = new QuestionMapping__c(FieldApiName__c='LastName',Question__c=q2.Id,SurveyMapping__c=sm1.Id);
        QuestionMapping__c qmLongText = new QuestionMapping__c(FieldApiName__c='mobilesurveys_Username__c',Question__c=qLongText.Id,SurveyMapping__c=sm1.Id);
        QuestionMapping__c qmDateTime = new QuestionMapping__c(FieldApiName__c='mobilesurveys_LastCheckInDate__c',Question__c=qContactDateTime.Id,SurveyMapping__c=sm1.Id);
            //Question mapping for the Account
        QuestionMapping__c qm3 = new QuestionMapping__c(FieldApiName__c='AccountNumber',Question__c=q1.Id,SurveyMapping__c=sm2.Id);
        QuestionMapping__c qm4 = new QuestionMapping__c(FieldApiName__c='Description',Question__c=q2.Id,SurveyMapping__c=sm2.Id);
            //Question mappings for opportunity
        QuestionMapping__c qmOpName = new QuestionMapping__c(FieldApiName__c='TotalOpportunityQuantity',Question__c=qTotalOpportunityQuantity2.Id,SurveyMapping__c=smOpportunity.Id);
        QuestionMapping__c qmOpTotalOpportunityQuantity = new QuestionMapping__c(FieldApiName__c='TotalOpportunityQuantity',Question__c=qTotalOpportunityQuantity.Id,SurveyMapping__c=smOpportunity.Id);
        QuestionMapping__c qmOpStageName = new QuestionMapping__c(FieldApiName__c='StageName',Question__c=qOpStageName.Id,SurveyMapping__c=smOpportunity.Id);
        QuestionMapping__c qmOpCloseDate = new QuestionMapping__c(FieldApiName__c='CloseDate',Question__c=qOpCloseDate.Id,SurveyMapping__c=smOpportunity.Id);
        QuestionMapping__c qmOpProbability = new QuestionMapping__c(FieldApiName__c='Probability',Question__c=qOpProbability.Id,SurveyMapping__c=smOpportunity.Id);
        QuestionMapping__c qmQuantity = new QuestionMapping__c(FieldApiName__c='TotalOpportunityQuantity',Question__c=q3.Id,SurveyMapping__c=smOpportunity.Id);
        QuestionMapping__c qmOpIsPrivate = new QuestionMapping__c(FieldApiName__c='IsPrivate',Question__c=qOpIsPrivate.Id,SurveyMapping__c=smOpportunity.Id);


        /*clean the enviroment for the assertions*/
        delete ([select Id From QuestionMapping__c]);

        /*Checking the correct insertion of some valid QuestionMapping__c objects instances*/

        insert new QuestionMapping__c[]{qm1,qm1clone,qm2clone,qm3,qm4};
        system.assertEquals(5,[select Id From QuestionMapping__c].size());

        /*Try to insert several QuestionMapping__c instances with invalid QuestionTypes/FieldTypes relations*/

        assertInsertionFails(qmLongText);
        assertInsertionFails(qmDateTime);
        assertInsertionFails(qmOpCloseDate);
        assertInsertionFails(qmOpStageName);
        assertInsertionFails(qmOpCloseDate);
        assertInsertionFails(qmOpProbability);
        assertInsertionFails(qmOpIsPrivate);
        assertInsertionFails(qm1duplicate);
        assertInsertionFails(qmOpName);
        assertInsertionFails(qmOpTotalOpportunityQuantity);
        assertInsertionFails(qmQuantity);
    }

    /*used to test the invalid QuestionMapping*/

    private static void assertInsertionFails(QuestionMapping__c qm){
        try{
            insert qm;
            system.assert(false);
        }catch (DMLException e){
            system.assert(true);
        }
    }
}
