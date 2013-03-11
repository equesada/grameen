public with sharing class SurveySettingsController{

    public Boolean hasError{get;set;}
    public Boolean isSettingSaved{get;set;}
    public Survey__c survey{get;set;}
    public SelectOption[] countriesToSelect{get;set;}
    public SelectOption[] PPIExistingSurveys{get;set;}
    public Id surveyToClone{get;set;}
    public String prevRadioButtonSelected{get; set;}
    public Boolean cloneGroupsCheckbox{get;set;}
       public Boolean isNotNew{get;set;}
    
    private static final Set<string> CONTACT_FIELDS_TO_MAP  = new Set<string>{'Name','Email','LastName'};
    public SelectOption[] contactFields;
    
    //Constants
    public static final String PPI_SURVEY = 'ppiSurvey';//PPI survey option
    public static final String CLONE_SURVEY = 'existingSurvey';//Clone Survey option
    public static final String BASE_SURVEY = 'baseSurvey';//Survey to clone
    public static final String START_FORM = 'group1';//Radio button selected
    public static final String CLONE_GROUPS_CHECKBOX ='checkBox';//Checkbox selected
    public static final String CLONE_GROUPS = 'cloneAssignments';//Clone groups option
    
    public Boolean isCloning{get;set;}
    public Boolean isPPI{
        get{
            if(this.survey.Id != null){
                return [SELECT IsPPI__c,Id from Survey__c where Id=:survey.Id].IsPPI__c ;
            }else
                return prevRadioButtonSelected == PPI_SURVEY || this.survey.IsPPI__c;
        }set;
    }

   
    public SurveySettingsController(ApexPages.StandardController stdController){
        this.prevRadioButtonSelected = system.currentPageReference().getParameters().get(START_FORM);
        isNotNew = false;
        hasError = false;
        this.cloneGroupsCheckbox=false;//Setting false as default
        String surveyId = System.currentPageReference().getParameters().get('id');
        //Selecting the correct survey.
        if(surveyId !='' && surveyId !=null){
         this.survey = [SELECT Status__c,Alias__c, Deployment__c,PPIDataYear__c, Name, IsPPI__c, Id,
                Gps_Location_Enabled__c, Description__c, CountryCode__c,Gps_ShowMap__c, Close_Message__c,InterviewMapField__c
                    From Survey__c WHERE Id =: surveyId];
            isNotNew=true;
        }else
            this.survey = (Survey__c)stdController.getRecord();

        countriesToSelect=new List<SelectOption>();//Countries
        PPIExistingSurveys=new List<SelectOption>();//PPI Surveys

        if(this.prevRadioButtonSelected == CLONE_SURVEY ){
            this.cloneGroupsCheckbox = system.currentPageReference().getParameters().get(CLONE_GROUPS_CHECKBOX) == CLONE_GROUPS;
            isCloning=true;
            String surveyToCloneName = system.currentPageReference().getParameters().get(BASE_SURVEY);
            //Select survey id to clone, and copy default fields to put in page
           Survey__c sur = [SELECT PPIDataYear__c,Deployment__c, Name,Alias__c, IsPPI__c, Id,
                Gps_Location_Enabled__c, Description__c,Gps_ShowMap__c, CountryCode__c, Close_Message__c, InterviewMapField__c
                    FROM Survey__c  WHERE Name =: surveyToCloneName];
            this.surveyToClone = sur.Id;
            this.survey.IsPPI__c = sur.IsPPI__c;
            if(!this.survey.IsPPI__c)
                this.survey.Name = sur.Name; //PPI Surveys Name is made with a trigger
            this.survey.Description__c = sur.Description__c ;
            this.survey.Deployment__c = sur.Deployment__c;
            this.survey.Alias__c = sur.Alias__c;
            this.survey.Gps_Location_Enabled__c= sur.Gps_Location_Enabled__c;
            this.survey.Gps_ShowMap__c = sur.Gps_ShowMap__c;
            this.survey.CountryCode__c = sur.CountryCode__c;
            this.survey.PPIDataYear__c = sur.PPIDataYear__c;
            this.survey.Gps_Location_Enabled__c = sur.Gps_Location_Enabled__c;
            this.survey.Close_Message__c = sur.Close_Message__c;
            this.survey.InterviewMapField__c = sur.InterviewMapField__c ;
        }

        //List of countries
        for(ISO_Country__c co:[SELECT Name, ShortName__c FROM ISO_Country__c ORDER BY ShortName__c LIMIT 1000]){
            countriesToSelect.add(new SelectOption( co.Name, co.ShortName__c ));
        }
        //List of PPISurveys
        for (Survey__c ppisur: [SELECT Name,Id,isPPI__c FROM Survey__c v WHERE (isPPI__c=true) ORDER  BY Name]){
            PPIExistingSurveys.add(new SelectOption(ppisur.Id,ppisur.Name));
        }
        //Do required Fields when is or is not PPI
        survey.IsPPI__c=isPPI;

    }
    //------------------------------------------------------------
    //License Control
    public PageReference checkLicense(){
       LicenseSettings__c license = C.getLicense();
       if( !license.PPI_Editable__c && isPPI )
            throw new C.LicenseException(system.label.SYSTEM_ERR_NOLICENSEPERMISSION);
       return null; 
    }
    //-------------------------------------------------------------

    /*
     * Check if the title of the survey does not exists
     */
    public PageReference validateTitleNotRepeated(){
        List<Survey__c> surveys = [SELECT Name
                                       FROM Survey__c
                                       WHERE Name= :survey.Name
                                       AND Id != :survey.Id];

        this.hasError = surveys.size()>0;
        if(hasError){
            survey.Name.addError( system.label.SURVEY_ERR_UNIQUETITLE );
        }
    return null;
    }

    /**
     * Refresh Survey DATA
     */
    public void refreshSurvey(){
        this.survey = [SELECT Status__c,Alias__c,Deployment__c, PPIDataYear__c, Name, IsPPI__c, Id,
            Gps_Location_Enabled__c, Description__c, CountryCode__c, Close_Message__c, InterviewMapField__c From Survey__c
                WHERE Id =: survey.Id];
    }

    /*
     * Save survey
     */
    public void save(){
        this.validateTitleNotRepeated();
        if(!this.hasError){
            try{
                upsert survey;
            }catch(Exception e){
            	 if(survey.Id != null)
                    refreshSurvey();
                this.hasError=true;
            }
        }
    }
    /*
     * Quick save survey
     */
    public PageReference quicksave(){
        saveSettingContinue();
        return null;
    }
    /*
     * Save setting survey on setting survey page, and redirect to question survey page
     */
    public Pagereference saveSettingContinue(){
        this.save();
        if(!this.hasError){
            // Move to Questions page, to fill questions for this new survey
            PageReference questionPage = Page.SurveyQuestion;
            if(this.prevRadioButtonSelected == CLONE_SURVEY){
                if(this.surveyToClone != null){
                    new SurveyUtils().cloneSurvey(this.surveyToClone,survey.Id,this.cloneGroupsCheckbox);
                }else{
                    this.hasError = true;
                    ApexPages.addMessage(new ApexPages.Message(ApexPages.Severity.ERROR, system.label.SURVEY_ERR_NOSURVEYSELECTED));
                    return null;
                }
            }
            questionPage.getParameters().put('id', this.survey.Id);
            return questionPage.setRedirect(true);
        }
        else
           return null;
    }

    /*
    Gets the list of availables field to map with the interviewee
    */
    public SelectOption[] getcontactFields(){
        contactFields = new Selectoption[]{};
        contactFields.add(new SelectOption('','- Do not ask for interviewee -'));
        Schema.DescribeSObjectResult contactObject = Schema.getGlobalDescribe().get('Contact').getDescribe();
        for(Schema.Sobjectfield f:contactObject.fields.getMap().values()){
           if(f.getDescribe().isExternalId()|| f.getDescribe().isUnique() || CONTACT_FIELDS_TO_MAP.contains(f.getDescribe().getName()))
                  contactFields.add(new SelectOption(f.getDescribe().getName(),f.getDescribe().getLabel()));
        }
        return contactFields;
    }
}