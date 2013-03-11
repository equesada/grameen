public with sharing class SurveyScoringController {

    public Survey__c survey{get; set;}                                  // Actual survey.
    public BuilderQuestion[] questions = new BuilderQuestion[]{};
    public List<ScoringGroup> listGroups = new List<ScoringGroup>{};      // Actual list of custom ScoringGroup.
    public List<ScoringValue> listScoreValues = new List<ScoringValue>{}; // Actual list of custom ScoringValues.
    public String scoringGroupStr {get; set;}                             //JSON string passed from the page with the group data.
    public String scoringValuesJson {get; set;}                           //JSON string passed from the page with the Score values data.
    public String continueAfterwards {get;set;}                           //String that decides if should stay in the page (quick save) or continue
    public boolean errorGroup = false;                                    // True if fails saving Groups.
    public boolean errorValues = false;                                   // True if fails saving Scoring values.

    public String getQuestions()      { return JSON.serialize(questions); }
    public String getListGroups()     { return JSON.serialize(listGroups); }
    public String getListScoreValues(){ return JSON.serialize(listScoreValues); }
    
    public final string PPI_ID = 'PPI';
    private final string PPI_LABEL = 'Progress out of poverty index';
    /*
    *    Class constructor
    */
    public SurveyScoringController (ApexPages.StandardController stdController) {
        this.survey = (Survey__c)stdController.getRecord();
        this.survey = [select Id,Name,isPPI__c,LastModifiedDate FROM Survey__c WHERE Id=:survey.id limit 1];
        
        // Obtain all questions and their options, for this survey.
        for(Question__c q: [Select Id, Name, Type__c, Parent__c, Caption__c, Hint__c, Position__c,
                            Minimum__c, Maximum__c, DoBefore__c, DoAfter__c, Required__c,
                            SkipLogicBehavior__c, SkipLogicOperator__c, CurrentTime__c, SamePage__c
                            From Question__c Where Survey__c = :survey.Id order by Position__c]){
            questions.add( new BuilderQuestion(q) );
        }

        for(Option__c o: [Select Id, Name, Position__c,Caption__c, Question__c, PPIScore__c
                          From Option__c Where Question__r.Survey__c = :survey.Id order by Position__c]){
            BuilderQuestion bq = findQuestion(o.Question__c);
            bq.options.add( new BuilderQuestion.Option(o) );

        }
        if(this.survey.isPPI__c){
            listGroups.add(new ScoringGroup(PPI_ID,PPI_LABEL));
            for(Option__c o: [Select Id, Name, Position__c,Caption__c, Question__c, PPIScore__c
                          From Option__c Where Question__r.Survey__c = :survey.Id order by Position__c]){
                listScoreValues.add(new ScoringValue(string.valueof(o.PPIScore__c),string.valueof(o.Id),PPI_ID));
            }
        }
        for(ScoringGroup__c sg : [select Id, Caption__c from ScoringGroup__c where Survey__c = :this.survey.Id]){
            ScoringGroup newScoring = new ScoringGroup(sg);
            listGroups.add(newScoring);
        }


        for(ScoreValue__c sv : [select Id, Value__c, Option__c, ScoringGroup__r.Caption__c
                                from ScoreValue__c where ScoringGroup__r.Survey__c = :this.survey.Id order by Id]){
            ScoringValue newScoreValue = new ScoringValue(sv, sv.ScoringGroup__r.Caption__c);
            listScoreValues.add(newScoreValue);
        }
    }
    //-------------------------------------------------------        
    //URL License Control, can't execute code without licence permission
    public PageReference checkLicense(){
       LicenseSettings__c license = C.getLicense();
       if( !license.PPI_Editable__c && this.survey.IsPPI__c )
            throw new C.LicenseException(system.label.SYSTEM_ERR_NOLICENSEPERMISSION);
       return null; 
    }
    //--------------------------------------------------------
    
    // Find a question on the list by it's id
    private BuilderQuestion findQuestion(String id) {
        BuilderQuestion questionReturn = null;
        for (BuilderQuestion q : questions){
            if (q.id == id){
                questionReturn = q;
            }
        }
        return questionReturn;
    }

    // Called from the page when clicking "Save and continue".
    public PageReference saveScoring() {

        PageReference pg = null;
        Survey__c dummySurvey = [select Id, Status__c, LastModifiedDate from Survey__c where Id = :this.survey.Id limit 1];
        if(dummySurvey.LastModifiedDate != survey.LastModifiedDate){
            ApexPages.message myMsg = new ApexPages.Message(ApexPages.Severity.ERROR, system.Label.BUILDER_ERR_CONCURRENTSAVE);
            ApexPages.addMessage(myMsg);
        }else if(dummySurvey.Status__c != 'Draft'){
            ApexPages.message myMsg = new ApexPages.Message(ApexPages.Severity.ERROR, system.label.BUILDER_ERR_SURVEYALREADYPUBLISHED);
            ApexPages.addMessage(myMsg);
        }else {
            deleteRelatedScoring();

            // Creates groups.
            if( this.scoringGroupStr != ''){
                JSONParser parserGroups = JSON.createParser(this.scoringGroupStr);
                List<ScoringGroup> customGroups = parserScoringGroup(parserGroups);
                List<ScoringGroup__c> savedGroups = saveGroups(customGroups);

                if (this.errorGroup) {
                    ApexPages.message myMsg = new ApexPages.Message(ApexPages.Severity.ERROR, 'An error ocurrs while saving Score group, check group data');
                    ApexPages.addMessage(myMsg);
                }
                else  {
                    // Creates score values
                    JSONParser parserValue = JSON.createParser(this.scoringValuesJson);
                    List<ScoringValue> scoringValues = parserScoringValues(parserValue);
                    map<String, Id> mapNameId = getMapNameIdGroups(savedGroups);
                    // Saves data
                    saveScoringValues(scoringValues, mapNameId);

                    if(this.errorValues){
                        ApexPages.message myMsg = new ApexPages.Message(ApexPages.Severity.ERROR, 'An error ocurrs while saving Score values, check scoring values');
                        ApexPages.addMessage(myMsg);
                    }else{
                        if (this.continueAfterwards == 'true'){
                            pg = this.survey.IsPPI__c ? Page.LookupTables : Page.SurveyQuestionLogic;
                            pg.getParameters().put('id', this.survey.Id);
                        }
                        //Update Survey
                        update survey;
                        this.survey = [select Id,Name,isPPI__c,LastModifiedDate FROM Survey__c WHERE Id=:survey.id limit 1];
                    }
                }
            }
        }
        return pg;
    }

    public void deleteRelatedScoring(){
        List<ScoringGroup__c> groupToDelete = [select Id from ScoringGroup__c where Survey__c = :this.survey.Id];
        delete groupToDelete;
    }

    /*
    *  Given a list<String> (names of the Scoring calculation groups), creates and insert each of these SCG.
    */
    private List<ScoringGroup__c> saveGroups(List<ScoringGroup> scoringGroups){
        List<ScoringGroup__c> realGroupList = new List<ScoringGroup__c>();
        if( scoringGroups != null && scoringGroups.size() > 0 ){
            for(ScoringGroup sg : scoringGroups){
            	if(sg.caption != PPI_ID){
                	ScoringGroup__c newCustomScoring = new ScoringGroup__c(Caption__c = sg.caption, Survey__c = this.survey.Id);
                	realGroupList.add(newCustomScoring);
            	}
            }
            try{
                insert realGroupList;
            }
            catch(Exception e){
                this.errorGroup = true;
            }
        }
        return realGroupList ;
    }

    /*
    *    Given a list of ScoringValue object (inner Class), and a map with the form <ScoringGroup__c.Caption, ScoringGroup__c.Id>,
    *    insert each Scoring value, and relate it with the correct ScoringGroup.
    */
    private void saveScoringValues(List<ScoringValue> scoringValues, map<String, Id> mapNameIdGroup){

        List<ScoreValue__c> realScoreValues = new List<ScoreValue__c>();
        if( scoringValues != null && scoringValues.size() > 0 ){
            List<Option__c> optionsToUpdatePPIScore = new List<Option__c>();
            for(ScoringValue sv : scoringValues){
                Id idOfGroup = mapNameIdGroup.get(sv.groupName);
                Decimal realValue = ( sv.value == null || sv.value == '' ) ? 0 : decimal.valueOf(sv.value);
                if(sv.groupName == PPI_ID){
                    Option__c opt = new Option__c(id = sv.optionId,PPIScore__c = realValue);
                    optionsToUpdatePPIScore.add(opt);
                }else{
                    ScoreValue__c newCustomScoreValue = new ScoreValue__c(Option__c = sv.optionId, ScoringGroup__c = idOfGroup, Value__c = realValue );
                    realScoreValues.add(newCustomScoreValue);
                }
            }
            try{
                insert realScoreValues;
                update optionsToUpdatePPIScore;
            }
            catch(Exception e){
                this.errorValues = true;
            }
        }
    }

    /*
    *  Given a list of ScoringGroup__c, returns a map with the form: <ScoringGroup__c.Value, ScoringGroup__c.Id>.
    */
    private map<String, Id> getMapNameIdGroups(List<ScoringGroup__c> listOfGroup){

        map<String, Id> mapNameId = new map<String, Id>();
        if( listOfGroup != null && listOfGroup.size() >0 ){
            for(ScoringGroup__c sg : listOfGroup){
                mapNameId.put(sg.Caption__c, sg.Id);
            }
        }
        return mapNameId;
    }

    /*
    *  Given a parser of scoringValues, returns a List<ScoringValue>.
    */
    private List<ScoringValue> parserScoringValues(JSONParser parser){
        List<ScoringValue> listOfScoringValues = new List<ScoringValue>();
        while (parser.nextToken() != null) {
            // Start at the array of ScoringValues.
            if (parser.getCurrentToken() == JSONToken.START_ARRAY) {
                while (parser.nextToken() != null) {
                    // Advance to the start object marker to
                    if (parser.getCurrentToken() == JSONToken.START_OBJECT) {
                        // Read entire ScoringValue object
                        ScoringValue sv = (ScoringValue)parser.readValueAs(ScoringValue.class);
                        listOfScoringValues.add(sv);
                    }
                }
            }
        }
        return listOfScoringValues;
    }

    /*
    *  Given a parser of scoringGroups, returns a List<ScoringGroup> .
    */
    private List<ScoringGroup> parserScoringGroup(JSONParser parser){
        List<ScoringGroup> listOfScoringGroup = new List<ScoringGroup>();
        while (parser.nextToken() != null) {
            // Start at the array of ScoringGroup.
            if (parser.getCurrentToken() == JSONToken.START_ARRAY) {
                while (parser.nextToken() != null) {
                    // Advance to the start object marker to
                    if (parser.getCurrentToken() == JSONToken.START_OBJECT) {
                        // Read entire ScoringGroup object
                        ScoringGroup sg = (ScoringGroup)parser.readValueAs(ScoringGroup.class);
                        listOfScoringGroup.add(sg);
                    }
                }
            }
        }
        return listOfScoringGroup;
    }


    //------- Classes---------

    /*
    *  ScoringValue "home made" object
    */
    class ScoringValue {
        public String value {get;set;}
        public String optionId {get;set;}
        public String groupName {get;set;}


        public ScoringValue(ScoreValue__c source, String nameGroup){
            this.value = String.valueOf(source.Value__c);
            this.optionId = source.Option__c;
            this.groupName = nameGroup;
        }

        public ScoringValue(string pValue, String pOptionId,string pGroupName){
            this.value = pValue;
            this.optionId = pOptionId;
            this.groupName = pGroupName;
        }

    }

    /*
    *  ScoringGroup "home made" object
    */
    class ScoringGroup{
        public String caption {get;set;}
        public String id {get;set;}

        public ScoringGroup(ScoringGroup__c source){
            this.caption = source.Caption__c;
            this.id = source.Id;
        }
        public ScoringGroup(string pCaption,string pId){
        	this.caption = pCaption;
            this.id = pId;
        }
    }

    //--------------------

}