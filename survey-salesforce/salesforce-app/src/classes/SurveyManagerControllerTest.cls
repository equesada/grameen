@isTest
public class SurveyManagerControllerTest{
    // Helper method to test empty strings
    private static Boolean empty(String s){
        return s == null || s.length() == 0;
    }
    public static Survey__c[] createSurveyList(){
        Survey__c[] surveys = new Survey__c[]{};
        SurveyVersion__c[] surveyVersions = new SurveyVersion__c[]{};
        for (Integer i = 1; i <= 25; i++){
            surveys.add(new Survey__c(Name = 'TestSurvey' + i));
        }
        insert surveys;

        return surveys;
    }
    static void newLicense(Boolean PPIEditable, String licName){
        list<LicenseSettings__c> lS = [SELECT PPI_Editable__c FROM LicenseSettings__c limit 1];
        delete lS;
        LicenseSettings__c li = new LicenseSettings__c(PPI_Editable__c = PPIEditable, Name = licName);
        insert li;
    }
    
    /**
        This method is to validate the status filter.
    */
    static testMethod void testValidateStatusFilter(){
        newLicense(false,'Test');
        Survey__c[] surveys = createSurveyList();

        SurveyManagerController controller = new SurveyManagerController();
        controller.filter='all';
        controller.doFilterStatus();

        system.assert(controller.resultSize >1);

        List<SelectOption> statusList = controller.statusList;
        surveys[0].status__c = 'Published';
        controller.filter='Published';
        controller.doFilterStatus();

    }

    /**
        This method is to validate the column sort.
    */
    static testMethod void testSortColumn(){
        newLicense(false,'Test');
        Survey__c[] surveys = createSurveyList();
        surveys[0].status__c = 'Published';
        update(surveys[0]);
        SurveyManagerController controller =  new SurveyManagerController();

        controller.previousSortField = null;
        controller.columnOrder = null;
        controller.stringOrder = 'asc';
        controller.doSort();
        Survey__c  firstPlaceSurveyFilterNull= controller.surveyList[0];
        controller.columnOrder = 'Status__c';
        controller.doSort();
        Survey__c  firstPlaceSurveyFilterStatus = controller.surveyList[0];
        system.assertNotEquals(firstPlaceSurveyFilterNull, firstPlaceSurveyFilterStatus);
        controller.previousSortField = 'Status__c';
        controller.doSort();

    }

    /**
        This method is to validate publish.
    */
    static testMethod void testDoPublish(){
        newLicense(false,'Test');
        Survey__c[] surveys = createSurveyList();              
        SurveyManagerController controller =  new SurveyManagerController();

        controller.surveyToPublish = null;

        try{
            controller.doPublish();
            system.assert(false, 'Should have failed here, since there\'s no survey.');
        }catch (SurveyException e){
            system.assertEquals(controller.ERR_NULL_REFERENCE_TO_SURVEY, e.getMessage());
        }

        controller.surveyToPublish = surveys[0].Id;//a fake survey ID
        delete surveys;
        try{
            controller.doPublish();
            system.assert(false, 'Should have failed here, since that survey does not exist.');
        }catch (SurveyException e){
            system.assert(e.getMessage().contains(controller.ERR_DOESNT_EXIST_SURVEYID.subString(0,20)));
        }

        Survey__c sur = new Survey__c(Name = 'Test');
        insert sur;
        SurveyVersion__c surveyVersion = [select Status__c from SurveyVersion__c where Survey__c =:sur.Id];
        surveyVersion.Status__c = 'Published';
        update surveyVersion;
        controller.surveyToPublish = sur.Id;
        try{
            controller.doPublish();
            system.assert(false, 'Should have failed here, since that survey does not exist.');
        }catch(SurveyException e){
            system.assert(e.getMessage().contains(controller.ERR_CANT_PUBLISH_WITH_STATUS.subString(0,20)));
        }

        Survey__c surs = new Survey__c(Name = 'Test1',Status__c='Draft');
        insert surs;
        controller.surveyToPublish = surs.Id;
        controller.doPublish();
        surs.Status__c = controller.surveyList[0].Status__c;
        system.assertEquals(surs.Status__c,'Published');

        try{
            controller.doPublish();
            system.assert(false, 'Should have failed here.');
        }catch(SurveyException e){
            system.assert(e.getMessage().contains(controller.ERR_CANT_PUBLISH_WITH_STATUS.subString(0,32)));
        }

    }

    /**
        This method is to validate moreActions/Close
    */
    static testMethod void testClose(){
        newLicense(false,'Test');
        Survey__c[] surveys = createSurveyList();              
        SurveyManagerController controller =  new SurveyManagerController();

        controller.doAction = 'Close';
        controller.surveyIdAction = null;

        try{
            controller.doMoreAction();
            system.assert(false, 'Should have failed since no survey is set for action.');
        }catch (SurveyException e){
            system.assert(e.getMessage().equals(controller.ERR_NULL_REFERENCE_TO_SURVEY));
        }

        controller.surveyIdAction = surveys[0].Id;//a fake survey ID
        delete surveys;
        try{
            controller.doMoreAction();
            system.assert(false, 'Should have failed since survey is not saved in database.');
        }catch (SurveyException e){
            system.assert(e.getMessage().contains(controller.ERR_DOESNT_EXIST_SURVEYID.subString(0,20)));
        }
        Survey__c surs = new Survey__c(Name = 'Test1');
        insert surs;
        controller.surveyIdAction = surs.id;
        controller.doMoreAction();
        surs.Status__c=controller.surveyList[0].Status__c;
        system.assertEquals(surs.Status__c,'Closed');

    }

    /**
        This method is to validate moreActions/Trash
    */
    static testMethod void testTrash(){
        newLicense(false,'Test');
        Survey__c[] surveys = createSurveyList();
        SurveyManagerController controller = new SurveyManagerController();

        controller.doAction = 'Delete';
        controller.surveyIdAction = null;

        try{
            controller.doMoreAction();
            system.assert(false, 'Should have failed since no survey is set for action.');
        }catch (SurveyException e){
            system.assert(e.getMessage().equals(controller.ERR_NULL_REFERENCE_TO_SURVEY));
        }

        controller.surveyIdAction = surveys[0].Id;//a fake survey ID
        delete surveys;

        controller.doMoreAction();
        system.assert(ApexPages.hasMessages(ApexPages.severity.ERROR));
        Survey__c surs = new Survey__c(Name = 'Test1');
        insert surs;

        //set the id of the survey to be trashed
        controller.surveyIdAction = surs.Id;
        //stores the survey to which the survey belongs
        string surveyId = surs.Id;

        controller.doMoreAction();
        list<Survey__c> listSurveysAfter = [Select Id from Survey__c where Id=: surveyId];
        //the survey should be deleted
        system.assertEquals(listSurveysAfter.size(), 0);

        Survey__c[] moreSurveys = createSurveyList();

        //set the id of the survey to be trashed
        controller.surveyIdAction = moreSurveys[5].id;

        //stores the survey to which the survey belongs
        String surveySpeId = moreSurveys[5].Id;

        controller.doMoreAction();
        list<Survey__c> listSurveysAfterForId = [Select Id from Survey__c where Id=: surveySpeId];
        //the survey should be deleted
        system.assertEquals(listSurveysAfterForId.size(), 0);

    }
    static testMethod void testPagination(){
        newLicense(false,'Test');
        Survey__c[] surveys = createSurveyList();              
        SurveyManagerController controller = new SurveyManagerController();
        system.assert(controller.pageSize==controller.MAX_ROWS_PER_PAGE);
        system.assert(controller.resultSize>0);

        system.assert(controller.hasNext);
        Integer actualPage= controller.pageNumber;
        controller.next();
        Integer nextPage = controller.pageNumber;
        system.assertNotEquals(actualPage, nextPage);
        system.assert(controller.hasPrevious);
        controller.previous();
        system.assertEquals(actualPage, controller.pageNumber);

    }

    static testMethod void TestDuplicate(){
        //PREPARING THE TEST
        newLicense(false,'Test');
        Survey__c survey = new Survey__c(Name = 'name',Status__c='Published');
        insert survey;
        Question__c sectionQuestion = new Question__c(Caption__c ='Section 1',
                Hint__c = 'sectionTest',
                Survey__c = survey.Id,
                Type__c = Questiontype.QUESTION_SECTION);
        insert sectionQuestion;
        SurveyVersion__c surveyVersion = [select Status__c from SurveyVersion__c where Survey__c =:survey.Id];
        surveyVersion.Status__c = 'Published';
        update surveyVersion;
        //PERFORMING THE TEST
        String cloneSurveyName = 'CLONE ' + survey.Name;
        String cloneSurvey2Name = 'CLONE1 ' + survey.Name;
        String cloneSurvey3Name = 'CLONE2 ' + survey.Name;
        SurveyManagerController smc = new SurveyManagerController();
        smc.doAction = 'Clone';
        smc.surveyIdAction = survey.Id;
        smc.doMoreAction();
        smc.doMoreAction();
        smc.doMoreAction();
        List<Survey__c> result = [select Name from Survey__c where Name =:cloneSurveyName];
        system.assertEquals(result.size(),1);
        //SECOND CLONE NAMING TEST
        result = [select Name from Survey__c where Name =:cloneSurvey2Name];
        system.assertEquals(result.size(),1);
        //THIRD CLONE NAMING TEST
        result = [select Name from Survey__c where Name =:cloneSurvey3Name];
        system.assertEquals(result.size(),1);
    }
}