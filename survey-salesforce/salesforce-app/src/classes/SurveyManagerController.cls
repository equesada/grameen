public with sharing class SurveyManagerController{
    public final String OK = 'OK';
    public final String ERR_DOESNT_EXIST_SURVEYID =    'Does not exist surveyId: {0}';
    public final String ERR_CANT_PUBLISH_WITH_STATUS =  'Can not publish a Survey with status: {0}';
    public final String ERR_NULL_REFERENCE_TO_SURVEY = 'Null reference to Survey.';
    public final Integer MAX_ROWS_PER_PAGE = 20; //Maybe, this number could be configured in a config or property file.
    public static final String CLONE_SURVEY_NAME = 'CLONE'; // used to append to a survey name in case of a Duplicate survey generation
    public ID surveyToPublish {get;set;}
    public String SurveyToPublishName{get;set;}
    public String contactGroupsIdsToPublish {get;set;}
    public List<ContactGroupSurveyAssignment__c> contactGroupsOfSurvey {get;set;}
    public boolean surveyHasGroups {get;set;}
    public boolean isPPIEnabled {get;set;}
    public LicenseSettings__c license{get;set;}
    public List<Survey__c> surveyList{
        get{            return (List<Survey__c>) paginationController.getRecords();
        }
        set;
    }
    public List<ContactGroup__c> completeGroupsList {get; set;}
    public String stringOrder{get;set;} //The last order
    public String doAction {get;set;}
    public String surveyIdAction {get;set;}
    public boolean displayGroupPopup {get; set;}
    public boolean cloneAllAssignments{get; set;}
    public List<SelectOption> statusList{
        get{
            statusList = new List<SelectOption>();
            statusList.add(new SelectOption('All','All'));
            Schema.DescribeFieldResult fieldResult = Survey__c.Status__c.getDescribe();
            List<Schema.PicklistEntry> statusListOptions = fieldResult.getPicklistValues();

            for( Schema.PicklistEntry f : statusListOptions){
                statusList.add(new SelectOption( f.getValue(),f.getLabel()));
            }
            return statusList;
        }
            set;
    }

    public String previousSortField{get;
        set{
            if (value == null || value.trim().equals('') || String.escapeSingleQuotes(value).contains('\\')){
                previousSortField = null;
            }else{
                previousSortField = value;
            }
        }
    }

    public String columnOrder {get;
        set{
            if (value == null || value.trim().equals('') || String.escapeSingleQuotes(value).contains('\\') ){
                columnOrder = 'Survey__c.CreatedDate';
            }else{
                 columnOrder = value.trim() ;
            }
        }
    }

    public String filter {get;
        set{
            if (value == null || value.trim().equals('') || String.escapeSingleQuotes(value).contains('\\')
                                                                        || value.equals('all')){
                filter = 'all';
            }else{
                filter = value;
            }
        }
    }

    public SurveyManagerController(){
        this.cloneAllAssignments = false;
        this.surveyHasGroups  = false;
        this.contactGroupsIdsToPublish='';
        this.completeGroupsList = [select Name,Id from ContactGroup__c where Status__c = 'Active'];
        this.filter = 'all';
        this.columnOrder = 'Survey__c.CreatedDate';
        stringOrder = 'desc';
        refreshPaginationController(columnOrder, stringOrder );
        this.surveyList = (List<Survey__c>) paginationController.getRecords();
    }

    //------------------------------------------------------------
    // Controller initialization script
    public void init(){
        // Execute version migration script
        new PostInstallScript().onInstall(null);

        // Load license settings
        license = C.getLicense();
        isPPIEnabled = license.PPI_Editable__c;
    }
    //-------------------------------------------------------------
    public PageReference doFilterStatus(){
        refreshPaginationController(columnOrder, stringOrder);
        return null;
    }

    public PageReference doSort(){
        String order = 'asc';

        /*This checks to see if the same header was click two times in a row, if so
        it switches the order.*/
        if(previousSortField == columnOrder){
            order = 'desc';
            previousSortField = null;
        }else{
            previousSortField = columnOrder;
        }
        stringOrder = ( stringOrder == 'asc' ) ? 'desc' : 'asc';
        integer Actualpagenumber = paginationController.getPageNumber();
        refreshPaginationController(columnOrder, stringOrder);
        paginationController.setPageNumber(Actualpagenumber);
        return null;
    }

    public PageReference doPublish(){
        if (this.surveyToPublish == null){
                SurveyException.raise(ERR_NULL_REFERENCE_TO_SURVEY);
        }else{
            Survey__c[] surveys = [SELECT id, Status__c FROM Survey__c WHERE id = :this.surveyToPublish];
            if (surveys.size() == 0)
                SurveyException.raise(ERR_DOESNT_EXIST_SURVEYID, this.surveyToPublish);
            else if(surveys[0].Status__c != 'Draft')
                SurveyException.raise(ERR_CANT_PUBLISH_WITH_STATUS, surveys[0].Status__c);
            else {
                List<ContactGroupSurveyAssignment__c> surveyContactGroups= new List<ContactGroupSurveyAssignment__c>();
                List<String> contactGroupsIds = this.contactGroupsIdsToPublish.split(',');
                for (String cg:contactGroupsIds){
                    if (cg!=''){
                        ContactGroupSurveyAssignment__c newCg = new ContactGroupSurveyAssignment__c (ContactGroup__c=Id.Valueof(cg),Survey__c=surveys[0].Id);
                        surveyContactGroups.add(newCg);
            }
        }
            SurveyVersion__c ver = [select Status__c FROM SurveyVersion__c WHERE Survey__c=:surveys[0].Id ORDER BY Version__c DESC LIMIT 1];
            ver.Status__c =  'Published';
            update ver;
                if(surveyContactGroups.size() > 0 ){
                    insert surveyContactGroups;
                }

            }
        }
        closeGroupPopup();
        refreshPaginationController(columnOrder, stringOrder );

        return null;
    }
    // ERROR MESSAGES
    public string error{get;set;}
    public static final String ERR_STRING_TOO_LONG = 'The Name of Survey is too long.';

    public PageReference doMoreAction(){
        PageReference returnExit = null;
        if (doAction == 'Close'){
            doCloseSurvey(surveyIdAction);

        }else if (doAction=='Clone'){
            try{
                error='';
                //Clone the survey
                Survey__c surToClone = [select Id,Name,Alias__c,Gps_Location_Enabled__c,IsPPI__c,Close_Message__c,Description__c, CountryCode__c, PPIDataYear__c,InterviewMapField__c,Deployment__c from Survey__c where id=:surveyIdAction][0];
                Survey__c clonedSur = surToClone.clone();
                clonedSur.Name = setCloneName(String.ValueOf(surToClone.Name));
                if( clonedSur.Name.length()> 80 ) SurveyException.raise(ERR_STRING_TOO_LONG);
                SurveyUtils su = new SurveyUtils();
                insert clonedSur;
                su.cloneSurvey(surToClone.Id ,clonedSur.Id,cloneAllAssignments);
            }catch(Exception ex){
                error = ex.getMessage();
            }

        }else if (doAction == 'Delete'){
            doTrashSurvey(surveyIdAction);

        }
        else if(doAction == 'editXForm' || doAction == 'viewXForm'){
            returnExit = new PageReference('/apex/editXForm?surveyId='+ surveyIdAction );
        }
        else if(doAction == 'Preview'){
            returnExit = new PageReference('/apex/SurveyPreview?id='+ surveyIdAction+'&b=0' );
        }

        refreshPaginationController(columnOrder, stringOrder );
        return returnExit;
    }

    private void doCloseSurvey(String surveyId){
        if (surveyId == null){
            SurveyException.raise(ERR_NULL_REFERENCE_TO_SURVEY);
        }
        try{
             SurveyVersion__c ver = [select Status__c FROM SurveyVersion__c WHERE Survey__c=:surveyId ORDER BY Version__c DESC LIMIT 1];
            ver.Status__c =  'Closed';
            update ver;

        }catch (QueryException qe){
            SurveyException.raise(ERR_DOESNT_EXIST_SURVEYID, surveyIdAction);
        }

    }

    private void doTrashSurvey(String surveyId){
        if (surveyId == null){
            SurveyException.raise(ERR_NULL_REFERENCE_TO_SURVEY);
        }
        try{
            Survey__c v = [SELECT Id FROM Survey__c WHERE Id = :surveyId];
            delete v;

            //if there are no other versions that belong to the same survey, the survey is also deleted

        }catch (QueryException qe){
            ApexPages.Message msg = new ApexPages.Message(ApexPages.severity.ERROR,
                                            String.format(ERR_DOESNT_EXIST_SURVEYID,new String[]{surveyId}));
            ApexPages.addMessage(msg);
        }

    }

    private void refreshPaginationController(String colOrder, String order){

        String queryString =
            'SELECT XForm_Status__c,StatusImage__c, Status__c,isPPI__c, PublishedDate__c, id, Name, ' +
            'Alias__c, CreatedDate, CreatedById, CreatedBy.Name, OwnerId, Owner.Name, ' +
            'LastModifiedDate, ResponseCount__c, QuestionCount__c  FROM Survey__c';

        if ((filter != null) && !filter.equalsIgnoreCase('all')){
            queryString = queryString + ' WHERE Status__c = \'' + filter +'\'';
        }

        queryString = queryString + ' ORDER BY ' +colOrder + ' '+order;

        Integer oldPageNumber = null;
        if (this.paginationController != null) oldPageNumber = this.paginationController.getPageNumber();
        this.paginationController = new ApexPages.StandardSetController(Database.getQueryLocator(queryString));
        this.paginationController.setPageSize(MAX_ROWS_PER_PAGE);

    }

    //Pagination Stuff
    public ApexPages.StandardSetController paginationController{get;set;}

      //Boolean to check if there are more records after the present displaying records
    public Boolean hasNext{
        get{ return paginationController.getHasNext();}
        set;
    }

    //Boolean to check if there are more records before the present displaying records
    public Boolean hasPrevious{
        get{ return paginationController.getHasPrevious();}
        set;
    }

    //Page number of the current displaying records
    public Integer pageNumber{
        get{ return paginationController.getPageNumber();}
        set;
    }

    //Page size of the current displaying records
    public Integer pageSize{
        get{ return paginationController.getPageSize();}
        set;
    }

    // Returns the number of records in the set.
    public Integer resultSize{
        get{ return paginationController.getResultSize();}
        set;
    }

    //Returns the previous page of records
    public void previous(){
        paginationController.previous();
    }

    //Returns the next page of records
    public void next(){
        paginationController.next();
    }


    /*Method to name a duplicated survey and not repeating the Survey Name,appending
    adding a consecutive number to the survey name*/

    public String setCloneName(String surveyName){
        String filter = CLONE_SURVEY_NAME + '%' + surveyName;
        String NameToAssign ='';
        boolean nameAssign = false;
        boolean FirstCloneCheck = true;

        List<Survey__c> surveys= [SELECT Name FROM Survey__c WHERE Name LIKE :filter order by Name asc];
        if (surveys.size()>0){
            Integer max = 0;
            Integer cloneNumber;
            for (Survey__c sur:surveys){
                String numString = sur.Name.split(' ',2)[0].replace('CLONE','');
                cloneNumber = numString == '' ? 0 : Integer.valueOf(numString);
                if (cloneNumber > max){
                    max=cloneNumber;
                }
            }
            max = max + 1 ;
            nameToAssign = CLONE_SURVEY_NAME + max + ' ' + surveyName;
            return NameToAssign;
        }else return (CLONE_SURVEY_NAME + ' ' +  surveyName);
    }
    public void closeGroupPopup() {
        displayGroupPopup = false;
    }
    public void showGroupPopup() {
        contactGroupsIdsToPublish = '';
        surveyHasGroups  = false;
        displayGroupPopup = true;
        this.contactGroupsOfSurvey = [select ContactGroup__r.Name,Id FROM ContactGroupSurveyAssignment__c where Survey__c=:surveyToPublish];
        if (contactGroupsOfSurvey.size()>0){
            this.surveyHasGroups = true;
        }
    }

    public PageReference newSurvey(){
        return Page.createSurvey;
    }
}