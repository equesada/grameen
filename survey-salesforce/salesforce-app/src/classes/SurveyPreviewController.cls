public with sharing class SurveyPreviewController {

    public Survey__c survey{get; set;} // Survey to be shown.
    public Survey__c surveyPreview {get;set;}// Holds the survey to be shown as a preview.
    public boolean fromNewSurvey {get; set;} // True if called from any page except SurveyManager. (Used to render or not some attributes in the page)
    public List<ScoringGroup> scoringGroups {get;set;} //Scoring groups related to this survey.
    public string builder{get;set;}
    public SurveyPreviewController(ApexPages.StandardController stdController) {
        builder = Apexpages.currentPage().getParameters().get('b');
        this.survey = (Survey__c)stdController.getRecord();
        setDataForPreview();
        List<BuilderQuestion.ScoreValue> scoreTempList = new List<BuilderQuestion.ScoreValue>();
        Set<Id> questionsId = new Set<Id>();

        // Obtain all questions and their options, for this survey.
        for(Question__c q: [Select Id, Name, Type__c, Parent__c, Caption__c, Hint__c, Position__c,
                            Minimum__c, Maximum__c, DoBefore__c, DoAfter__c, Required__c,
                            CurrentTime__c, SkipLogicBehavior__c, SkipLogicOperator__c, SamePage__c
                            From Question__c Where Survey__c = :survey.Id]){
            sfquestions.put(q.Id, q);
            questionsId.add(q.Id);
            questions.add( new BuilderQuestion(q) );
        }
        // Obtain ScoreValues for questions selected.
        for(ScoreValue__c sv : [select Id, Option__c, Value__c, ScoringGroup__c  from ScoreValue__c
                                    where Option__r.Question__c in :questionsId order by ScoringGroup__r.CreatedDate desc ]){
            BuilderQuestion.ScoreValue newScore = new BuilderQuestion.ScoreValue(sv);
            scoreTempList.add(newScore);
        }
        // Obtain Options for the given survey.
        for(Option__c o: [Select Id, Name, Position__c,Caption__c, Question__c
                          From Option__c Where Question__r.Survey__c = :survey.Id order by position__c]){
            sfoptions.put(o.Id, o);
            BuilderQuestion bq = findQuestion(o.Question__c);
            BuilderQuestion.Option tempOption = new BuilderQuestion.Option(o);
            tempOption = addCorrectScoreValues(tempOption, scoreTempList);
            bq.options.add( tempOption );

        }
        //Obtain SkipConditions for the selected survey.
        for(SkipCondition__c c: [Select Id, Parent__c, SourceQuestion__c, Condition__c, Negate__c, Value__c
                                 From SkipCondition__c where Parent__r.Survey__c = :survey.Id]){
            findQuestion(c.Parent__c).skipconditions.add(new BuilderQuestion.SkipCondition(c));
        }

        scoringGroups = new List<ScoringGroup>();
        for(ScoringGroup__c sg : [select Id, Name, Caption__c from ScoringGroup__c where Survey__c = :survey.Id order by CreatedDate]){
            ScoringGroup newScoringGroup = new ScoringGroup(sg);
            scoringGroups.add( newScoringGroup );
        }
    }

    // Sets the preview Survey__c object and data to be shown.
    public void setDataForPreview(){
        this.fromNewSurvey = true;
        if( this.survey != null ) {
            surveyPreview = [select Id, Name, Gps_Location_Enabled__c,
                             Alias__c, Deployment__c, Close_Message__c,status__c from Survey__c where Id = :this.survey.Id ][0];
        }

        String referer = ApexPages.currentPage().getHeaders().get('Referer');

        if (referer != null){
            String lowerCaseReferer = ApexPages.currentPage().getHeaders().get('Referer').toLowerCase();
            integer refererIndex = lowerCaseReferer.indexOf('surveymanager');
            this.fromNewSurvey = (refererIndex == -1);
        }

    }

    public BuilderQuestion[] questions        = new BuilderQuestion[]{}; // Contains questions to be upserted on the next "save" call.
    public String getQuestions(){ return JSON.serialize(questions); }

        // Maps to keep correspondence between builder objects and actual Salesforce objects.
    private Map<String,Question__c> sfquestions = new Map<String,Question__c>();
    private Map<String,Option__c> sfoptions = new Map<String,Option__c>();

    // HELPER METHODS : GENERAL

    // Find a question on the list by it's id
    private BuilderQuestion findQuestion(String id) {
        for (BuilderQuestion q : questions)
            if (q.id == id)
                return q;
        return null;
    }

    /*
    *  Given a BuilderQuestion.Option, and a List of BuilderQuestion.ScoreValues, adds the related scoreValues
    *  to the given BuilderQuestion.Option.
    */
    private BuilderQuestion.Option addCorrectScoreValues(BuilderQuestion.Option opt, List<BuilderQuestion.ScoreValue> scoreList ){
        if(scoreList != null && scoreList.size() > 0){
            String idOpt = opt.Id;
            for( BuilderQuestion.ScoreValue sv : scoreList ){
                if(sv.optionId == idOpt){
                    opt.scoreValues.add(sv);
                }
            }
        }
        return opt;
    }

    public class ScoringGroup {
        public String scoringNumber {get;set;}
        public String caption {get;set;}

        public ScoringGroup(ScoringGroup__c source){
            this.scoringNumber = source.Name;
            this.caption = source.Caption__c;
        }

    }
}