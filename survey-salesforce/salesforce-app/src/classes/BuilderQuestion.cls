public with sharing class BuilderQuestion {

    //----------
    // CONSTANTS
    //----------

    public static final String ERR_MISSING_REQ_FIELD = 'Required field is missing: {0}';
    public static final String ERR_INVALID_OPERATION = 'Attempted to perform an invalid operation: {0}';
    public static final String ERR_NO_SURVEY_SPECIFIED = 'No survey was specified for this question yet.';

    public static final String OP_CHANGE_QUESTION_TYPE = 'Change question type';

    //-------
    // FIELDS
    //-------

    public String id;     // id to identify this question during construction
    public String parent; // id of the parent question, if any

    public String caption;
    public String name;
    public String qhint;
    public Integer position;
    public Boolean required;
    public Boolean samePage;
    public string skipLogicBehavior;
    public string skipLogicOperator;
    public Decimal minimum;
    public Decimal maximum;
    public String doAfter;
    public String doBefore;
    public String type;
    public Option[] options = new Option[]{};
    public Set<String> deletedOptions = new Set<String>();
    public SkipCondition[] skipconditions = new SkipCondition[]{};
    public Boolean currentTime;
    public List<ScoreValue> scoreValues;

    //-------------
    // CONSTRUCTORS
    //-------------

    // Creates a new BuilderQuestion object from a json input
    public static BuilderQuestion fromJson(String jsonQuestion, Boolean checkRequiredFields) {
        JSONParser parser = JSON.createParser(jsonQuestion);
        Type builderQuestionType = Type.forName('BuilderQuestion');
        BuilderQuestion q = (BuilderQuestion)parser.readValueAs(builderQuestionType);

        if (checkRequiredFields){
            if (q.type == null) SurveyException.raise(ERR_MISSING_REQ_FIELD, 'type');
        }

        // If id was not specified on the json string, generate a new id for this question
        if (q.id == null) q.id = Datetime.now().getTime().format();
        return q;
    }

    // Creates a new BuilderQuestion object from a Question__c input
    public BuilderQuestion(Question__c question) {

        id          		= question.Id;
        parent     			= question.Parent__c;

        type        		= question.Type__c;
        required    		= question.Required__c;
        samePage            = question.SamePage__c;
        skipLogicBehavior 	= question.SkipLogicBehavior__c;
        skipLogicOperator 	= question.SkipLogicOperator__c;
        caption     		= question.Caption__c;
        name        		= question.Name;
        qhint       		= question.Hint__c;
        position    		= Integer.valueOf(question.Position__c);

        // Number questions
        minimum     		= question.Minimum__c;
        maximum     		= question.Maximum__c;
        doBefore    		= question.DoBefore__c;
        doAfter     		= question.DoAfter__c;

        // Datetime questions
        currentTime 		= question.CurrentTime__c;
    }

    //Generate id
    private String newId(){
        return Datetime.now().getTime().format();
    }

    //--------
    // METHODS
    //--------

    public Option getOption(String id){
        if (this.options != null)
            for(Option o: this.options)
                if (o.id == id)
                    return o;
        return null;
    }

    public void copyTo(BuilderQuestion target) {
        // Copy the list of options

        // 0. Use temporary lists, in case there is an error and a rollback is needed
        Option[] ops = new Option[]{};
        Set<String> del = new Set<String>();
        if (target.deletedOptions != null) del.addAll(target.deletedOptions);

        // 1. Update existing options
        // 2. Remove options that no longer exist
        // -- Variables: t(arget), s(ource), n(ew)
        if (target.options != null) {
            for(Option t : target.options) {
                Option s = this.getOption(t.id);
                if (s != null) {
                    Option n = new Option();
                    t.copyTo(n);
                    s.copyTo(n);
                    ops.add(n);
                } else {
                    del.add(t.id);
                }
            }
        }

        // 3. Add newly created options
        if (this.options != null) {
            for(Option s : this.options) {
                Option t = target.getOption(s.id);
                if (t == null) {
                    ops.add(s);
                }
            }
        }

        // 4. Sort options (insert-sort by position)
        for(Integer start = 0; start < ops.size(); start++){
            Integer minval = 9999, minpos = 9999;
            for(Integer pos = start; pos < ops.size(); pos++){
                Option o = ops[pos];
                if (o.position < minval) { minval = o.position; minpos = pos; }
            }
            Option picked = ops[minpos];
            ops[minpos] = ops[start];
            ops[start] = picked;
        }

        // 5. Check that option positions are valid (secuential, unique, complete)
        final String REPEATED_POS = 'Two or more options share the same position for question {0}.';
        final string MISSING_POS = 'There are missing positions on the option list for question {0}. There are {1} options, but none has position {2}.';
        Integer lastpos = 9999;
        for (Integer pos = 0; pos < ops.size(); pos++) {
            Option o = ops[pos];
            if (o.position == lastpos) SurveyException.raise(REPEATED_POS, this.id);
            if (o.position > pos+1) SurveyException.raise(MISSING_POS, new String[]{this.id, String.valueOf(ops.size()), String.valueOf(pos+1)});
            lastpos = o.position;
        }

        // Now that everything's ok, replace the values on target for real
        target.id					= id;
        target.parent				= parent;
        target.caption				= caption;
        target.name					= name;
        target.qhint				= qhint;
        target.required				= required;
        target.samePage             = samePage;
        target.skipLogicBehavior	= skipLogicBehavior;
        target.skipLogicOperator	= skipLogicOperator;
        target.position				= position;
        target.type					= type;

        // Number type questions
        target.minimum				= minimum;
        target.maximum				= maximum;
        target.doBefore				= doBefore;
        target.doAfter				= doAfter;

        // Datetime type questions
        target.currentTime			= currentTime;

        target.options				= ops;
        target.deletedOptions		= del;
    }

    public Question__c createQuestion(Survey__c survey) {
        Question__c question = new Question__c(Survey__c = survey.id);
        updateQuestion(question);
        return question;
    }

    public void updateQuestion(Question__c question) {
        question.Caption__c				= caption;
        question.Name					= name;
        question.Hint__c				= qhint;
        question.Required__c			= required != null && required;
        question.SamePage__c            = samePage != null && samePage;
        question.SkipLogicBehavior__c	= skipLogicBehavior;
        question.SkipLogicOperator__c	= skipLogicOperator;
        question.Minimum__c				= minimum ;
        question.Maximum__c				= maximum ;
        question.DoBefore__c			= doBefore;
        question.DoAfter__c				= doAfter;
        question.Type__c				= type;
        question.Position__c			= position;
        question.CurrentTime__c			= currentTime != null && currentTime;
    }

    public class Option {
        public String id;
        public String name;
        public String caption;
        public Integer position;
        public List<ScoreValue> scoreValues;

        public Option(){}
        public Option(Option__c option){
            this.id       = option.Id;
            this.name     = option.name;
            this.caption  = option.Caption__c;
            this.position = Integer.valueOf(option.Position__c);
            this.scoreValues = new List<ScoreValue>();
        }

        public void copyTo(Option target){
            target.id       = id;
            target.name     = name;
            target.caption  = caption;
            target.position = position;
        }

        public Option__c createOption(Question__c question) {
            Option__c option = new Option__c(Question__c = question.id);
            updateOption(option);
            return option;
        }

        public void updateOption(Option__c option) {
            option.Caption__c        = caption;
            option.name        = name;
            option.Position__c = position;
        }

    }

    public class SkipCondition {
        public String id;
        public String sourceQuestionId;
        public String condition;
        public String value;
        public Boolean negate;

        public SkipCondition(){}
        public SkipCondition(SkipCondition__c source){
            this.id               = source.Id;
            this.sourceQuestionId = source.SourceQuestion__c;
            this.condition        = source.Condition__c;
            this.value            = source.Value__c;
            this.negate           = source.Negate__c;
        }

        public void copyTo(SkipCondition target){
            target.id               = id;
            target.sourceQuestionId = sourceQuestionId;
            target.condition        = condition;
            target.value            = value;
            target.negate           = negate;
        }

        public SkipCondition__c createSkipCondition(Question__c question){
            SkipCondition__c skipcond = new SkipCondition__c(Parent__c = question.Id);
            updateSkipCondition(skipcond);
            return skipcond;
        }

        public void updateSkipCondition(SkipCondition__c target){
            target.SourceQuestion__c = this.sourceQuestionId;
            target.Condition__c      = this.condition;
            target.Value__c          = this.value;
            target.Negate__c         = this.negate;
        }
    }

    public class ScoreValue{

        public String id;
        public String optionId;
        public String ScoringGroupId;
        public Decimal value;

        public ScoreValue(ScoreValue__c source){
            this.id             = source.Id;
            this.optionId       = source.Option__c;
            this.ScoringGroupId = source.ScoringGroup__c;
            this.value          = source.Value__c;
        }

    }
}