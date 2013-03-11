@isTest
private class SurveySettingsControllerTest {

    private static final String TITLE = '__Test__ Title';
    private static final String ANOTHER_TITLE = '__Test__ Another Title';
    //This method delete de original license and create a new one
    static LicenseSettings__c newLicense(Boolean PPIEditable, String licName){
        list<LicenseSettings__c> lS = [SELECT PPI_Editable__c FROM LicenseSettings__c limit 1];
        delete lS;
        LicenseSettings__c li = new LicenseSettings__c(PPI_Editable__c = PPIEditable, Name = licName);
        return li;
    }
    /*
     * this method insertS one survey and try to validate another with the same TITLE.
     * expects error message
     */
    static testMethod void validateTitleRepeatedTest() {
        //INIT TEST CASE
        LicenseSettings__c testWithLicense = newLicense(false,'Test1');
        insert testWithLicense;
        Survey__c survey = new Survey__c(Name = TITLE);
        insert survey;
        //PERFORMING TEST
        Survey__c anotherSurvey = new Survey__c(Name = ANOTHER_TITLE);
        insert anotherSurvey;
        ApexPages.currentPage().getParameters().put('id',anotherSurvey.Id);
        ApexPages.Standardcontroller stdController = new ApexPages.StandardController(anotherSurvey);
        SurveySettingsController extension = new SurveySettingsController(stdController);
        extension.survey.Name = TITLE;
        extension.validateTitleNotRepeated();
        //PERFORMING ASSERTION
        System.assert(extension.hasError);
        system.assertEquals(extension.saveSettingContinue(), null);
    }

    /*
     * This method inserts one survey and then validates another with different TITLE
     * Expects no error message
     */
    static testMethod void validateTitleNotRepeatedTest() {
        //INIT TEST CASE
        LicenseSettings__c testWithLicense = newLicense(false,'Test1');
        insert testWithLicense;
        Survey__c survey = new Survey__c(Name = TITLE);
        insert survey;
        //PERFORMING TEST
        Survey__c anotherSurvey = new Survey__c(Name=ANOTHER_TITLE);
        insert anotherSurvey;
        ApexPages.Standardcontroller stdController = new ApexPages.StandardController(anotherSurvey);
        SurveySettingsController extension = new SurveySettingsController(stdController);
        extension.validateTitleNotRepeated();
        // PERFORMING ASSERTION
        System.assert(!extension.hasError);
    }
     static testMethod void checkQuickSave(){
        //PERFORMING TEST
        LicenseSettings__c testWithLicense = newLicense(false,'Test1');
        insert testWithLicense;
        Survey__c survey = new Survey__c(Name=TITLE);
        ApexPages.Standardcontroller stdController = new ApexPages.StandardController(survey);
        SurveySettingsController extension = new SurveySettingsController(stdController);
        extension.survey.Name = ANOTHER_TITLE;
        extension.quicksave();
        //PERFORMING ASSERTION
        List<Survey__c> surveys = [SELECT s.Name from Survey__c s where s.Name=:ANOTHER_TITLE];
        System.assertEquals(1, surveys.size());
    }
    /*
     * Test for saveSettingContinue method.
     */
    static testMethod void saveSettingContinueTest(){
        //PERFORMING TEST
        LicenseSettings__c testWithLicense = newLicense(false,'Test1');
        insert testWithLicense;
        Survey__c survey = new Survey__c(Name=TITLE);
        ApexPages.Standardcontroller stdController = new ApexPages.StandardController(survey);
        SurveySettingsController extension = new SurveySettingsController(stdController);
        PageReference pg = extension.saveSettingContinue();
        List<Survey__c> surveyList = [select s.Id,s.Name from Survey__c s where s.Id = :survey.Id];
        PageReference createdPage = Page.SurveyQuestion;
        system.assert(pg != null, 'There was an error saving the survey.');
        system.assertEquals(pg.getUrl() , createdPage.getUrl() + '?id=' + surveyList[0].Id );
    }
    // Test for clone survey radio button
    static testMethod void saveSettingContinueClone(){
        LicenseSettings__c testWithLicense = newLicense(false,'Test1');
        insert testWithLicense;
        Survey__c survey = new Survey__c(Name='this is a test');
        Survey__c surveyToClone = new Survey__c(Name='Survey To Clone');
        insert surveyToClone;
        system.currentPageReference().getParameters().put('group1', 'existingSurvey');
        system.currentPageReference().getParameters().put('baseSurvey', 'Survey To Clone');
        ApexPages.Standardcontroller stdController = new ApexPages.StandardController(survey);
        SurveySettingsController extension = new SurveySettingsController(stdController);
        survey.Name = 'this is a test';
        extension.saveSettingContinue();
        system.assertEquals(survey.Name,'this is a test');
        system.assert(!extension.hasError);

        // Test with a null survey to clone
        extension.surveyToClone = null;
        extension.saveSettingContinue();
        system.assert(extension.hasError);
    }
    //Test for save and continue and ppis surveys
     static testMethod void saveSettingContinueNotSuccess(){
        //test a survey with repeated names
        Survey__c survey = new Survey__c(Name='__test1');
        insert survey;
        Survey__c surveyWithError = new Survey__c(Name='__test1');
        ApexPages.Standardcontroller stdController = new ApexPages.StandardController(surveyWithError);
        SurveySettingsController extension = new SurveySettingsController(stdController);
        extension.saveSettingContinue();
        system.assert(extension.hasError);
        //PPI survey with the same country-year
        ISO_Country__c countryCode = new ISO_Country__c(Name='AA',ShortName__c='Test');
        insert countryCode;
        Survey__c surveyPPI = new Survey__c(IsPPI__c=true ,CountryCode__c='AA',PPIDataYear__c = 2103);
        Survey__c surveyPPIwithError = new Survey__c(IsPPI__c=true,CountryCode__c='AA',PPIDataYear__c = 2103);
        insert surveyPPI;
        ApexPages.Standardcontroller stdControllerPPI = new ApexPages.StandardController(surveyPPIwithError);
        SurveySettingsController extensionPPI = new SurveySettingsController(stdControllerPPI);
        extensionPPI.saveSettingContinue();
        system.assert(extensionPPI.hasError);
        System.assertEquals(2,ApexPages.getMessages().size());
        System.assertEquals('There is already a PPI Survey for this Country and Year. Please select another country or year.',ApexPages.getMessages().get(1).getDetail());
        System.assertEquals(ApexPages.Severity.ERROR,ApexPages.getMessages().get(1).getSeverity());

    }
    /*
     * Test for saveSettingContinue method with survey in the header.
     */

    static testMethod void saveSettingContinueSurveyIncludedTest(){
        LicenseSettings__c testWithLicense = newLicense(false,'Test1');
        insert testWithLicense;
        //PERFORMING TEST
        Survey__c survey = new Survey__c(Name=TITLE);
        insert survey;
        ApexPages.Standardcontroller stdController = new ApexPages.StandardController(survey);
        SurveySettingsController extension = new SurveySettingsController(stdController);
        PageReference pg = extension.saveSettingContinue();
        PageReference createdPage = new PageReference('/apex/surveyquestion');
        system.assert(pg != null, 'There was an error saving the survey.');
        system.assertEquals(pg.getUrl() , createdPage.getUrl() + '?id=' + survey.Id );
    }
    static testMethod void checkLicense(){
        ISO_Country__c countryCode = new ISO_Country__c(Name='AA',ShortName__c='Test');
        insert countryCode;
        Survey__c survey = new Survey__c(IsPPI__c=true,CountryCode__c='AA',PPIDataYear__c = 2103);
        insert survey;
        LicenseSettings__c testLicense = newLicense(false,'Test');
        insert testLicense;
        try{
            ApexPages.Standardcontroller stdController = new ApexPages.StandardController(survey);
	    }catch(Exception e){
	        system.assertEquals( system.label.SYSTEM_ERR_NOLICENSEPERMISSION, e.getMessage());   
	    }
	    LicenseSettings__c testWithLicense = newLicense(true,'Test1');
	    insert testWithLicense;
        ApexPages.Standardcontroller stdController = new ApexPages.StandardController(survey);
    }
    static testMethod void cloneSurveyTest(){
        // PREPARING TEST
        LicenseSettings__c testWithLicense = newLicense(false,'Test1');
        insert testWithLicense;
        
        Survey__c survey = new Survey__c(Name='This is a test', Status__c='Published');
        insert survey;

        Question__c section= new Question__c(Caption__c='Test section',Type__c=QuestionType.QUESTION_SECTION,Survey__c=survey.Id);
        insert section;

        Question__c sourceQuestion = new Question__c(Name='sq',Caption__c='__test__ sourceq',SkipLogicBehavior__c='Show',SkipLogicOperator__c='All',Type__c=QuestionType.QUESTION_TEXT_SHORT,Parent__c=section.Id,Survey__c=survey.Id,Position__c=1);
        Question__c targetQuestion = new Question__c(Name='tq',Caption__c='__test__ targetq',SkipLogicBehavior__c='Hide',SkipLogicOperator__c='Any',Type__c=QuestionType.QUESTION_TEXT_SHORT,Parent__c=section.Id,Survey__c=survey.Id,Position__c=2);
        insert (new List<Question__c>{sourceQuestion,targetQuestion});

        Option__c option = new Option__c(Position__c=1,Name='testOption',Question__c=sourceQuestion.Id);
        insert option;

        SkipCondition__c sk=new SkipCondition__c(Parent__c=targetQuestion.Id,SourceQuestion__c=sourceQuestion.Id,Value__c='Test',Condition__c = 'equal');
        insert sk;

        Survey__c cloned = new Survey__c(Name = 'this is cloned');
        insert cloned;

        ScoringGroup__c scoreGroup= new ScoringGroup__c(Survey__c = survey.Id,Caption__c = 'Score group 1');
        insert scoreGroup;

        ScoreValue__c scoreValue = new ScoreValue__c(ScoringGroup__c = scoreGroup.Id,option__c=option.Id,Value__c=12);
        insert scoreValue;

        SurveyMapping__c surveyMapping = new SurveyMapping__c(MatchingField__c='LastName',
                                            ObjectApiName__c='Contact',
                                            Survey__c=survey.Id);
        insert surveyMapping;

        QuestionMapping__c questionMapping = new QuestionMapping__c(FieldApiName__c='LastName',
                                            Question__c=sourceQuestion.Id,
                                            SurveyMapping__c=surveyMapping.Id);
        insert questionMapping;
        //EXECUTING  TEST IF IS NOT PPI
        new SurveyUtils().cloneSurvey(survey.Id,cloned.Id,true);
        List <Survey__c> result = [select Name from Survey__c ORDER BY Id desc];
        Question__c[] sections  = [select Parent__r.Id,Name, Caption__c, Survey__c from Question__c where survey__c = :cloned.Id and Type__c  = :QuestionType.QUESTION_SECTION ORDER BY Parent__c asc];
        Question__c[] questions = [select Parent__r.Id,Name, Caption__c, Survey__c from Question__c where survey__c = :cloned.Id and Type__c != :QuestionType.QUESTION_SECTION ORDER BY Parent__r.Position__c asc, Position__c asc];
        SkipCondition__c clonedSkipCondition = [select Parent__r.Name,SourceQuestion__r.Name,Parent__r.Id,Value__c,Condition__c from SkipCondition__c where Parent__r.Survey__c = :cloned.Id][0];
        ScoringGroup__c clonedScoringGroup = [select Id,Caption__c,Survey__c from ScoringGroup__c where Survey__c = :cloned.Id][0];
        ScoreValue__c clonedScoreValue = [select Value__c,Id,ScoringGroup__c from ScoreValue__c where ScoringGroup__c = :clonedScoringGroup.Id][0];
        SurveyMapping__c clonedSurveyMapping = [select Id,MatchingField__c,ObjectApiName__c,Survey__c from SurveyMapping__c where Survey__c = :cloned.Id][0];
        QuestionMapping__c clonedQuestionMapping= [select FieldApiName__c,Id,Question__c,Question__r.Id,SurveyMapping__c from QuestionMapping__c where SurveyMapping__c = :clonedSurveyMapping.Id][0];
        //ASSERTIONS

        //checking that the cloned section contains the sourceQuestion
        system.assertEquals(sections[0].Id,questions[0].Parent__r.Id);
        //checking that the cloned section is not contained by anything alse
        system.assertEquals(null,sections[0].Parent__c);
        //checking that actually clonedsurvey != survey
        system.assertNotEquals(cloned.Id, survey.Id);


        //checking that a skipCondition object was created,and that it's values are the correct ones
        system.assertNotEquals(null,clonedSkipCondition);
        system.assertEquals(targetQuestion.Name,clonedSkipCondition.Parent__r.Name);
        system.assertEquals(sourceQuestion.Name,clonedSkipCondition.SourceQuestion__r.Name);
        system.assertEquals(sk.Value__c,clonedSkipCondition.Value__c);
        system.assertEquals(sk.Condition__c,clonedSkipCondition.Condition__c);
        Question__c[] sourceQuestions = [select Name,Caption__c,Hint__c,Parent__r.Name,Position__c,Type__c,SkipLogicOperator__c,SkipLogicBehavior__c from Question__c where Survey__c = :survey.Id order by Parent__r.Position__c asc, Position__c asc];
        Question__c[] clonedQuestions = [select Name,Caption__c,Hint__c,Parent__r.Name,Position__c,Type__c,SkipLogicOperator__c,SkipLogicBehavior__c from Question__c where Survey__c = :cloned.Id order by Parent__r.Position__c asc, Position__c asc];

        // Assert that question attributes were correctly cloned
        system.assertEquals(3, sourceQuestions.size());
        system.assertEquals(3, clonedQuestions.size());
        for (Integer q=0;q<sourceQuestions.size();q++){
            Question__c sq = sourceQuestions[q];
            Question__c cq = clonedQuestions[q];
            system.assertEquals(sq.Name, cq.Name);
            system.assertEquals(sq.Caption__c, cq.Caption__c);
            system.assertEquals(sq.Hint__c, cq.Hint__c);
            system.assertEquals(sq.Parent__r.Name, cq.Parent__r.Name);
            system.assertEquals(sq.Position__c, cq.Position__c);
            system.assertEquals(sq.Type__c, cq.Type__c);
            system.assertEquals(sq.SkipLogicBehavior__c, cq.SkipLogicBehavior__c);
            system.assertEquals(sq.SkipLogicOperator__c, cq.SkipLogicOperator__c);
        }

        //checking that a cloned sourceQuestion is actually different of the original one
        Question__c clonedQuestion = [select Id from Question__c where Survey__c = :cloned.Id and Name = :sourceQuestion.Name];
        system.assertNotEquals(sourceQuestion.Id,clonedQuestion.Id);
        sourceQuestion.Hint__c = 'edited hint';
        upsert sourceQuestion;
        clonedQuestion = [select Id, Hint__c from Question__c where Id = :clonedQuestion.Id];
        system.assertNotEquals(sourceQuestion.Hint__c, clonedQuestion.Hint__c);

        //checking option cloning
        Option__c clonedOption = [select Name,Position__c from Option__c where Question__r.Survey__c = :cloned.Id];
        system.assertNotEquals(option.Id,clonedOption.Id);
        system.assertEquals(option.Position__c,clonedOption.Position__c);
        system.assertEquals(option.Name,clonedOption.Name);

        //checking that Scoring objects were created,and that their values are the correct ones
        system.assertNotEquals(scoreGroup,clonedScoringGroup);
        system.assertEquals(scoreGroup.Caption__c,clonedScoringGroup.Caption__c);
        system.assertEquals(clonedScoringGroup.Survey__c,cloned.Id);
        system.assertNotEquals(clonedScoreValue,scoreValue);
        system.assertEquals(clonedScoreValue.ScoringGroup__c,clonedScoringGroup.Id);
        system.assertEquals(clonedScoreValue.Value__c,scoreValue.Value__c);

        //checking that the Mapping objects were created,with correct values
        system.assertNotEquals(surveyMapping,clonedSurveyMapping);
        system.assertEquals(surveyMapping.ObjectApiName__c,clonedSurveyMapping.ObjectApiName__c);
        system.assertEquals(surveyMapping.MatchingField__c,clonedSurveyMapping.MatchingField__c);
        system.assertNotEquals(questionMapping,clonedQuestionMapping);
        system.assertEquals(questionMapping.FieldApiName__c,clonedQuestionMapping.FieldApiName__c);
        system.assertEquals(clonedQuestion.Id,clonedQuestionMapping.Question__r.Id);
        system.assertNotEquals(questionMapping.SurveyMapping__c,clonedSurveyMapping.Id);
        //Cleaning original surveys
        ISO_Country__c countryCode = new ISO_Country__c(Name='AA',ShortName__c='Test');
        insert countryCode;
        Survey__c surveyPpi = new Survey__c(IsPPI__c=true,CountryCode__c='AA',PPIDataYear__c = 2123);
        insert surveyPpi;
        Survey__c clonedPpi = new Survey__c(IsPPI__c=true,CountryCode__c='AA',PPIDataYear__c = 2133);
        insert clonedPpi;
        PPITable__c originalTable = new PPITable__c(Name = 'TableTest');
        insert originalTable;
        PPITableDataSet__c originalDataSet = new PPITableDataSet__c(PPITable__c = originalTable.Id, Survey__c = surveyPpi.Id);
        insert originalDataSet;
        PPITableLine__c originalTableLine = new PPITableLine__c(From__c = 10, To__c = 30, Probability__c = 80, PPITableDataSet__c = originalDataSet.Id);
        insert originalTableLine;
        new SurveyUtils().cloneSurvey(surveyPpi.Id, clonedPpi.Id,false);
        PPITable__c clonedTable = [SELECT Name FROM PPITable__c WHERE Name = :'TableTest'];
        PPITableDataSet__c clonedDataSet = [SELECT Id,Survey__c,PPITable__c FROM PPITableDataSet__c WHERE Survey__c = :clonedPpi.Id AND PPITable__c = :clonedTable.Id];
        PPITableLine__c clonedTableLine = [SELECT Id,From__c,To__c,Probability__c,PPITableDataSet__c FROM PPITableLine__c WHERE PPITableDataSet__c = :clonedDataSet.Id];
        system.assertEquals( originalTableLine.Probability__c, clonedTableLine.Probability__c ); 
        system.assertEquals( originalTableLine.From__c , clonedTableLine.From__c );
        system.assertEquals( originalTableLine.To__c , clonedTableLine.To__c );
        system.assertNotEquals( originalTableLine.PPITableDataSet__c, clonedTableLine.PPITableDataSet__c );
        system.assertEquals( originalTable.Name, clonedTable.Name ); 
        system.assertEquals( originalDataSet.PPITable__c, clonedDataSet.PPITable__c ); 
    }

}