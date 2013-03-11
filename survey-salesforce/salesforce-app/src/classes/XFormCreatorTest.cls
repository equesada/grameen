@isTest
private class XFormCreatorTest {
    /*Constants*/
    //class to keep the most used nodes in the xml
    public class XFormNodes{
        public dom.document document;
        public dom.xmlnode xmlRoot;
          public dom.xmlnode xmlBody;
          public dom.xmlnode xmlModel;
          public dom.xmlnode xmlData;
    }

    public static XFormNodes createCleanXForm(String SurveyId){
    //dom Xml generation
        XFormNodes XFormResult= new XFormNodes();

        dom.Document doc= new dom.Document();
        XFormResult.document = doc;
           dom.Xmlnode xmlRoot = doc.createRootElement('h:html',null,null);
           XFormResult.xmlRoot = xmlRoot;
           xmlRoot.setNamespace('jr','http://openrosa.org/javarosa');
           xmlRoot.setNamespace('h','http://www.w3.org/1999/xhtml');
           dom.Xmlnode xmlHead = xmlRoot.addChildElement('h:head',null,null);
        xmlHead.addChildElement('h:title',null,null).addTextNode('name');
        dom.Xmlnode xmlModel = xmlHead.addChildElement('model',null,null);
        XFormResult.xmlModel = xmlModel;
        dom.Xmlnode xmlInstance = xmlModel.addChildElement('instance',null,null);
        dom.Xmlnode xmlData = xmlInstance.addChildElement('data',null,null);
        XFormResult.xmlData = xmlData;
        //here goes the model questions
        xmlData.setAttribute('id',SurveyId);
        XFormCreator.setHeaderModel(xmlData);
        XFormCreator.setHeaderBindings(xmlModel);
           //here go the bindings questions
           dom.Xmlnode xmlBody = xmlRoot.addChildElement('h:body',null,null);
           XFormResult.xmlBody = xmlBody;
        //here goes the question body
        dom.Xmlnode xmlStandardQuestions = XmlBody.addChildElement('group',null,null);
        xmlStandardQuestions.setAttribute('appearance','field-list');
        dom.Xmlnode xmlIntervieweeInput = xmlStandardQuestions.addChildElement('input',null,null);
        xmlIntervieweeInput.setAttribute('ref','/data/subscriberid');
        xmlIntervieweeInput.addChildElement('label',null,null).addTextNode('Who are you interviewing?');
        xmlIntervieweeInput.addChildElement('hint',null,null).addTextNode('Please enter the interviewee\'s Id');
        dom.Xmlnode xmlLocationInput = xmlStandardQuestions.addChildElement('input',null,null);
        xmlLocationInput.setAttribute('ref','/data/location');
        xmlLocationInput.setAttribute('appearance','maps');
        xmlLocationInput.addChildElement('label',null,null).addTextNode('GPS Location');
        xmlLocationInput.addChildElement('hint',null,null).addTextNode('This will capture your current location');
        return XFormResult;
    }

    //Static question generated for every survey
    public static void addSimpleInputQuestionBody(dom.xmlnode xmlBody,String path,String label,String questionHint){
        dom.Xmlnode xmlQuestion = xmlBody.addChildElement('input',null,null);
        xmlQuestion.setAttribute('ref',path);
        xmlQuestion.addChildElement('label',null,null).addTextNode(label);
        xmlQuestion.addChildElement('hint',null,null).addTextNode(questionHint);
    }
    public static void addSelectItem(dom.xmlnode xmlBody,String label,String value){
        dom.Xmlnode xmlQuestion = xmlBody.addChildElement('item',null,null);
        xmlQuestion.addChildElement('label',null,null).addTextNode(label);
        xmlQuestion.addChildElement('value',null,null).addTextNode(value);
    }
    public static void addBindingNode(dom.xmlnode xmlModel,String questionPath,String questionType,boolean required,String questionConstraints,String questionconstraintMsg,String relevants){
        dom.xmlNode xmlBindQuestion = xmlModel.addChildElement('bind',null,null);
        xmlBindQuestion.setAttribute('nodeset',questionPath);
        xmlBindQuestion.setAttribute('type',questionType);
        if (required){
            xmlBindQuestion.setAttribute('required','true()');
        }
        if(questionConstraints!=''){
            xmlBindQuestion.setAttribute('jr:constraint',questionConstraints);
            if (questionConstraintMsg!=''){
                   xmlBindQuestion.setAttribute('jr:constraintMsg',questionConstraintMsg);
            }
        }
        if (relevants!=''){
            xmlBindQuestion.setAttribute('relevant',relevants);
        }
    }

    private static Question__c standardQuestion(String name,Id survey,Id parent){
        Question__c question = new Question__c(
            Caption__c ='How old are you?',
            Hint__c = 'Tell me Your age',
            Survey__c = survey,
            Type__c = QuestionType.QUESTION_TEXT_SHORT,
            Parent__c = parent
        );
        return question;
    }

    // Helper method to create skip conditions cases
    // Will be used by testSkipConditionFormation
    private static void newConditionCase(Question__c section, String questionName, Integer questionPosition,
            Question__c sourceQuestion, String condition, String value, Boolean negate,List<Question__c> questions,List<SkipCondition__c> conditions){
        Question__c targetQuestion = new Question__c(
            Survey__c = section.Survey__c,
            Parent__c = section.Id,
            Name = questionName,
            Caption__c = questionName,
            Position__c = questionPosition,
            Type__c = Questiontype.QUESTION_STATIC);
        questions.add(targetQuestion);

        SkipCondition__c skipCondition = new SkipCondition__c(
            Parent__c = targetQuestion.Id,
            SourceQuestion__c = sourceQuestion.Id,
            Condition__c = condition,
            Value__c = value,
            Negate__c = negate);
        conditions.add(skipCondition);
    }

    // Test different combinations of Show/Hide, All/Any, and zero, one or two conditions
    static testMethod void testSkipConditionCombination(){

        // Set up the test survey
        //-----------------------
        Survey__c survey = new Survey__c(Name='name'); insert survey;

        Question__c section = new Question__c(Survey__c=survey.Id,Name='s',Caption__c='s',Type__c=QuestionType.QUESTION_SECTION,Position__c=1);
        insert section;

        Integer pos = 1;
        // Question used as base for conditions
        Question__c sq = new Question__c(Survey__c=survey.Id,Name='sq',Caption__c='sq',Type__c=QuestionType.QUESTION_TEXT_LONG,Parent__c=section.Id,Position__c=pos++);

        // No conditions
        Question__c q01 = new Question__c(Survey__c=survey.Id,Name='q01',Caption__c='q01',Type__c=QuestionType.QUESTION_TEXT_LONG,Parent__c=section.Id,Position__c=pos++,SkipLogicBehavior__c='Show',SkipLogicOperator__c='All');
        Question__c q02 = new Question__c(Survey__c=survey.Id,Name='q02',Caption__c='q02',Type__c=QuestionType.QUESTION_TEXT_LONG,Parent__c=section.Id,Position__c=pos++,SkipLogicBehavior__c='Show',SkipLogicOperator__c='Any');
        Question__c q03 = new Question__c(Survey__c=survey.Id,Name='q03',Caption__c='q03',Type__c=QuestionType.QUESTION_TEXT_LONG,Parent__c=section.Id,Position__c=pos++,SkipLogicBehavior__c='Hide',SkipLogicOperator__c='All');
        Question__c q04 = new Question__c(Survey__c=survey.Id,Name='q04',Caption__c='q04',Type__c=QuestionType.QUESTION_TEXT_LONG,Parent__c=section.Id,Position__c=pos++,SkipLogicBehavior__c='Hide',SkipLogicOperator__c='Any');

        // One condition
        Question__c q11 = new Question__c(Survey__c=survey.Id,Name='q11',Caption__c='q11',Type__c=QuestionType.QUESTION_TEXT_LONG,Parent__c=section.Id,Position__c=pos++,SkipLogicBehavior__c='Show',SkipLogicOperator__c='All');
        Question__c q12 = new Question__c(Survey__c=survey.Id,Name='q12',Caption__c='q12',Type__c=QuestionType.QUESTION_TEXT_LONG,Parent__c=section.Id,Position__c=pos++,SkipLogicBehavior__c='Show',SkipLogicOperator__c='Any');
        Question__c q13 = new Question__c(Survey__c=survey.Id,Name='q13',Caption__c='q13',Type__c=QuestionType.QUESTION_TEXT_LONG,Parent__c=section.Id,Position__c=pos++,SkipLogicBehavior__c='Hide',SkipLogicOperator__c='All');
        Question__c q14 = new Question__c(Survey__c=survey.Id,Name='q14',Caption__c='q14',Type__c=QuestionType.QUESTION_TEXT_LONG,Parent__c=section.Id,Position__c=pos++,SkipLogicBehavior__c='Hide',SkipLogicOperator__c='Any');

        // Two conditions
        Question__c q21 = new Question__c(Survey__c=survey.Id,Name='q21',Caption__c='q21',Type__c=QuestionType.QUESTION_TEXT_LONG,Parent__c=section.Id,Position__c=pos++,SkipLogicBehavior__c='Show',SkipLogicOperator__c='All');
        Question__c q22= new Question__c(Survey__c=survey.Id,Name='q22',Caption__c='q22',Type__c=QuestionType.QUESTION_TEXT_LONG,Parent__c=section.Id,Position__c=pos++,SkipLogicBehavior__c='Show',SkipLogicOperator__c='Any');
        Question__c q23= new Question__c(Survey__c=survey.Id,Name='q23',Caption__c='q23',Type__c=QuestionType.QUESTION_TEXT_LONG,Parent__c=section.Id,Position__c=pos++,SkipLogicBehavior__c='Hide',SkipLogicOperator__c='All');
        Question__c q24= new Question__c(Survey__c=survey.Id,Name='q24',Caption__c='q24',Type__c=QuestionType.QUESTION_TEXT_LONG,Parent__c=section.Id,Position__c=pos++,SkipLogicBehavior__c='Hide',SkipLogicOperator__c='Any');

        insert new Question__c[]{sq,q01,q02,q03,q04,q11,q12,q13,q14,q21,q22,q23,q24};

        addConditions(q11,sq,1);
        addConditions(q12,sq,1);
        addConditions(q13,sq,1);
        addConditions(q14,sq,1);
        addConditions(q21,sq,2);
        addConditions(q22,sq,2);
        addConditions(q23,sq,2);
        addConditions(q24,sq,2);

        // Assert conditions are correctly generated
        //------------------------------------------
        final String COND = '/data/s/sq!=\'\''; // all individual conditions will be the same: sq is answered
        Map<String,String> expectedRelevants = new Map<String,String>{
            'q01' => null,
            'q02' => null,
            'q03' => null,
            'q04' => null,
            'q11' => COND,
            'q12' => COND,
            'q13' => 'not(' + COND + ')',
            'q14' => 'not(' + COND + ')',
            'q21' => COND + ' and ' + COND,
            'q22' => COND + ' or ' + COND,
            'q23' => 'not(' + COND + ' and ' + COND + ')',
            'q24' => 'not(' + COND + ' or ' + COND + ')'
        };

        XFormCreator creator = new XFormCreator();
        dom.Document xform = creator.getXFormFromSurvey(survey,survey.name);

        dom.XmlNode root = xform.getRootElement();
        dom.XmlNode head = root.getChildElement('h:head',null);
        dom.XmlNode model = head.getChildElement('model',null);
        dom.XmlNode[] bindings = model.getChildren();
        for (dom.XmlNode child : bindings){
            if ('bind' != child.getName()) continue;

            String nodeset = child.getAttribute('nodeset',null);
            if (!nodeset.startsWith('/data/s/')) continue;

            String relevant = child.getAttribute('relevant',null);
            String key = nodeset.replace('/data/s/','');
            system.assertEquals(expectedRelevants.get(key), relevant, 'Nodeset: ' + nodeset);
        }
    }

    // Helper method for testSkipConditionCombination
    private static void addConditions(Question__c target, Question__c source, Integer nrOfConditions){
        while(0 < nrOfConditions){
            insert new SkipCondition__c(
                Parent__c = target.Id,
                SourceQuestion__c = source.Id,
                Condition__c = QuestionType.SKIPLOGIC_CONDITION_ISANSWERED,
                Value__c = null,
                Negate__c = false);
            nrOfConditions--;
        }
    }

    static testMethod void testSkipConditionFormation(){

        // Create base survey and first section
        Survey__c survey = new Survey__c(Name='name',Gps_Location_Enabled__c=true,InterviewMapField__c = 'Name');
        insert survey;

        Question__c section = new Question__c(Name='s',Caption__c ='s',
                Hint__c = 's',
                Survey__c = survey.Id,
                Type__c = Questiontype.QUESTION_SECTION);

        insert section;

        // Create some different type questions to use as source for different conditions
        Question__c qsing = new Question__c(Survey__c=survey.Id,Name='radio' ,Caption__c='radio' ,Type__c=QuestionType.QUESTION_RADIO     ,Parent__c=section.Id,Position__c=1);
        Question__c qnumb = new Question__c(Survey__c=survey.Id,Name='number',Caption__c='number',Type__c=QuestionType.QUESTION_NUMBER    ,Parent__c=section.Id,Position__c=2);
        Question__c qtext = new Question__c(Survey__c=survey.Id,Name='text'  ,Caption__c='text'  ,Type__c=QuestionType.QUESTION_TEXT_SHORT,Parent__c=section.Id,Position__c=3);
        insert new Question__c[]{qsing, qnumb, qtext};

        Option__c option = new Option__c(Name='a',Caption__c='a',Question__c=qsing.Id,Position__c=1);
        insert option;

        // Create a section that will hold all test questions
        Question__c ts = new Question__c(Survey__c=survey.Id,Name='t',Caption__c='t',Position__c=2,Type__c=QuestionType.QUESTION_SECTION);
        insert ts;

        Integer pos = 1; // positions to assign to test questions

        //List of the objects to insert
        List<Question__c> questions = new List<Question__c>();
        List<SkipCondition__c> conditions = new List<SkipCondition__c>();
        // From radio questions
        newConditionCase(ts,'radio1',pos++,qsing,QuestionType.SKIPLOGIC_CONDITION_EQUAL, String.valueOf(option.Id), false,questions,conditions);
        newConditionCase(ts,'radio2',pos++,qsing,QuestionType.SKIPLOGIC_CONDITION_EQUAL, String.valueOf(option.Id), true,questions,conditions);
        newConditionCase(ts,'radio3',pos++,qsing,QuestionType.SKIPLOGIC_CONDITION_ISANSWERED, '', false,questions,conditions);
        newConditionCase(ts,'radio4',pos++,qsing,QuestionType.SKIPLOGIC_CONDITION_ISANSWERED, '', true,questions,conditions);

        // From number questions;
        newConditionCase(ts,'number1',pos++,qnumb,QuestionType.SKIPLOGIC_CONDITION_EQUAL, '9', false,questions,conditions);
        newConditionCase(ts,'number2',pos++,qnumb,QuestionType.SKIPLOGIC_CONDITION_EQUAL, '9', true,questions,conditions);
        newConditionCase(ts,'number3',pos++,qnumb,QuestionType.SKIPLOGIC_CONDITION_GREATER, '9', false,questions,conditions);
        newConditionCase(ts,'number4',pos++,qnumb,QuestionType.SKIPLOGIC_CONDITION_GREATER, '9', true,questions,conditions);
        newConditionCase(ts,'number5',pos++,qnumb,QuestionType.SKIPLOGIC_CONDITION_LESSER, '9', false,questions,conditions);
        newConditionCase(ts,'number6',pos++,qnumb,QuestionType.SKIPLOGIC_CONDITION_LESSER, '9', true,questions,conditions);
        newConditionCase(ts,'number7',pos++,qnumb,QuestionType.SKIPLOGIC_CONDITION_ISANSWERED, '', false,questions,conditions);
        newConditionCase(ts,'number8',pos++,qnumb,QuestionType.SKIPLOGIC_CONDITION_ISANSWERED, '', true,questions,conditions);

        // From text questions;
        newConditionCase(ts,'text1',pos++,qtext,QuestionType.SKIPLOGIC_CONDITION_EQUAL, 'test', false,questions,conditions);
        newConditionCase(ts,'text2',pos++,qtext,QuestionType.SKIPLOGIC_CONDITION_EQUAL, 'test', true,questions,conditions);
        newConditionCase(ts,'text3',pos++,qtext,QuestionType.SKIPLOGIC_CONDITION_ISANSWERED, '', false,questions,conditions);
        newConditionCase(ts,'text4',pos++,qtext,QuestionType.SKIPLOGIC_CONDITION_ISANSWERED, '', true,questions,conditions);
        insert questions;
        Integer i=0;
        for (SkipCondition__c sk:conditions){
            sk.Parent__c=questions[i].Id;
            i++;
        }
        insert conditions;
        Map<String,String> expectedRelevants = new Map<String,String>{
            '/data/t/radio1' => 'selected(/data/s/radio,\'' + option.Id + '\')',
            '/data/t/radio2' => 'not(selected(/data/s/radio,\'' + option.Id + '\'))',
            '/data/t/radio3' => '/data/s/radio!=\'\'',
            '/data/t/radio4' => '/data/s/radio=\'\'',

            '/data/t/number1' => '/data/s/number=9',
            '/data/t/number2' => '/data/s/number!=9',
            '/data/t/number3' => '/data/s/number>9',
            '/data/t/number4' => '/data/s/number<=9',
            '/data/t/number5' => '/data/s/number<9',
            '/data/t/number6' => '/data/s/number>=9',
            '/data/t/number7' => '/data/s/number!=\'\'',
            '/data/t/number8' => '/data/s/number=\'\'',

            '/data/t/text1' => '/data/s/text=\'test\'',
            '/data/t/text2' => '/data/s/text!=\'test\'',
            '/data/t/text3' => '/data/s/text!=\'\'',
            '/data/t/text4' => '/data/s/text=\'\''
        };

        XFormCreator creator = new XFormCreator();
        dom.Document xform = creator.getXFormFromSurvey(survey,survey.name);

        dom.XmlNode root = xform.getRootElement();
        dom.XmlNode head = root.getChildElement('h:head',null);
        dom.XmlNode model = head.getChildElement('model',null);
        dom.XmlNode[] bindings = model.getChildren();

        // Check that the relevants for static questions are the expected ones
        dom.XmlNode bindstat = null;
        for (dom.XmlNode child : bindings){
            if (child.getName() != 'bind') continue;
            String nodeset =child.getAttribute('nodeset',null);
            if( nodeset != null && nodeset.startsWith('/data/t/') ){
                String relevant = child.getAttribute('relevant',null);
                system.assertEquals( expectedRelevants.get(nodeset), relevant );
            }
        }
    }

    // Helper class to ensure that correct attributes have been set for each binding element.
    class Binding{
        String type;
        String jrPreload;
        String jrPreloadParams;
        String required;
        String readOnly;
        String jrConstraint;
        String jrConstraintMsg;
        public Binding(String type,String jrPreload,String jrPreloadParams,String required,String readOnly,String jrConstraint,String jrConstraintMsg){
            this.type = type;
            this.jrPreload = jrPreload;
            this.jrPreloadParams = jrPreloadParams;
            this.required = required;
            this.readOnly = readOnly;
            this.jrConstraint = jrConstraint;
            this.jrConstraintMsg = jrConstraintMsg;
        }
    }

    static  testMethod void testSkipConditionGeneration(){
        final String CLOSE_MESSAGE = 'close</closemessage> &a'; // try to break the xml

        //PREPARING THE TEST

        Survey__c survey = new Survey__c(Name='name',Gps_Location_Enabled__c=true, Close_Message__c = CLOSE_MESSAGE,InterviewMapField__c = 'Name');
        insert survey;
        Question__c sectionQuestion = new Question__c(Caption__c ='Section 1',
                Hint__c = 'sectionTest',
                Survey__c = survey.Id,
                Type__c = Questiontype.QUESTION_SECTION,Name='SectionOne');

        insert sectionQuestion;

        sectionQuestion = [select Name,Hint__c,Caption__c from Question__c where Id=:sectionQuestion.Id];
        Question__c questionSource = standardQuestion('question', survey.Id,sectionQuestion.Id);
        questionSource.Required__c = true;
        questionSource.Name= 'questionSource';
        questionSource.Position__c=1;
        Question__c dateQuestion = new Question__c(Caption__c ='date',
                    Hint__c = 'a datetime',Parent__c = sectionQuestion.Id,
                    Survey__c = survey.Id,
                    Type__c = QuestionType.QUESTION_DATE,Position__c=2,Name='dateQuestion');
        Question__c questionNumberSource = new Question__c(Caption__c ='Number With Max and Min',
                    Hint__c = 'between 0 and 10',Parent__c = sectionQuestion.Id,
                    Survey__c = survey.Id,Minimum__c=0,Maximum__c=10,
                    Type__c = QuestionType.QUESTION_NUMBER,Required__c=true,
                    Name='numberQuestion',Position__c=3);
        Question__c checkboxQuestion = new Question__c(Caption__c ='select options',
                    Hint__c = 'select an option',Parent__c = sectionQuestion.Id,
                    Survey__c = survey.Id,
                    Type__c = QuestionType.QUESTION_CHECKBOX,Required__c=false,
                    Name='checkboxQuestion',Position__c=4);
        Question__c radioQuestion = new Question__c(Caption__c ='select one option',
                    Hint__c = 'select an option',Parent__c = sectionQuestion.Id,
                    Survey__c = survey.Id,Required__c=true,
                    Type__c = QuestionType.QUESTION_RADIO,
                    SkipLogicOperator__c='Any',
                    SkipLogicBehavior__c='Hide',
                    Name='radioQuestion',Position__c=5);
        Question__c questionParent = standardQuestion('question', survey.Id,sectionQuestion.Id);
        questionParent.Required__c = true;
        questionParent.Name= 'questionParent';
        questionParent.Position__c=6;
        Question__c longQuestion = new Question__c(Caption__c ='MultiText',
                    Hint__c = 'a hint',Parent__c = sectionQuestion.Id,
                    Survey__c = survey.Id,
                    Type__c = QuestionType.QUESTION_TEXT_LONG,Position__c=8,Name='longQuestion');
        Question__c dateTimeQuestion = new Question__c(Caption__c ='dateTime',
                    Hint__c = 'a datetime',Parent__c = sectionQuestion.Id,
                    Survey__c = survey.Id,
                    Type__c = QuestionType.QUESTION_DATETIME,Position__c=9,Name='dateTimeQuestion');
        Question__c maxNumberQuestion = new Question__c(Caption__c ='Number With Max and no Min',
                    Hint__c = 'less than 5',Parent__c = sectionQuestion.Id,
                    Survey__c = survey.Id,Maximum__c=5,
                    Type__c = QuestionType.QUESTION_NUMBER,Required__c=false,
                    Name='maxQuestion',Position__c=10);
        Question__c minNumberQuestion = new Question__c(Caption__c ='Number With Min and no Max',
                    Hint__c = 'more than 5',Parent__c = sectionQuestion.Id,
                    Survey__c = survey.Id,Minimum__c=5,
                    Type__c = QuestionType.QUESTION_NUMBER,Required__c=false,
                    Name='minQuestion',Position__c=11);
        Question__c staticQuestion = new Question__c(Caption__c ='Static question',
                    Parent__c = sectionQuestion.Id,
                    Survey__c = survey.Id,
                    Type__c = QuestionType.QUESTION_STATIC,
                    Name='static',Position__c=12);
        List<Question__c> questions = new List<Question__c>{questionSource,dateQuestion,questionNumberSource,checkboxQuestion,radioQuestion,questionParent,longQuestion,dateTimeQuestion,maxNumberQuestion,minNumberQuestion,staticQuestion};
        insert questions;
        Option__c opACheckBox= new Option__c(Name='option1',Caption__c='option1',Question__c=checkboxQuestion.Id,Position__c=1);
        Option__c opBCheckBox= new Option__c(Name='option2',Caption__c='option2',Question__c=checkboxQuestion.Id,Position__c=2);
        Option__c opCCheckBox= new Option__c(Name='option3',Caption__c='option3',Question__c=checkboxQuestion.Id,Position__c=3);
        Option__c opARadio= new Option__c(Name='option1',Caption__c='option1',Question__c =radioQuestion.Id,Position__c=1);
        Option__c opBRadio= new Option__c(Name='option2',Caption__c='option2',Question__c=radioQuestion.Id,Position__c=2);
        Option__c opCRadio= new Option__c(Name='option3',Caption__c='option3',Question__c=radioQuestion.Id,Position__c=3);

        List<Option__c> options = new List<Option__c>{opACheckBox,opBCheckBox,opCCheckBox,opARadio,opBRadio,opCRadio};
        insert options;

        //PERFORMING THE TEST
        XFormCreator x = new XFormCreator();
        dom.document xform = x.getXFormFromSurvey(survey,survey.name);

        dom.XmlNode root = xform.getRootElement();
        dom.XmlNode head = root.getChildElement('h:head',null);

        // Assert title is there and is right
        dom.XmlNode title = head.getChildElement('h:title',null);
        system.assertEquals('name',title.getText());

        dom.XmlNode model = head.getChildElement('model',null);
        dom.XmlNode instance = model.getChildElement('instance',null);
        dom.XmlNode data = instance.getChildElement('data',null);

        // Assert model is there, and data node contains all first level children
        String[] expectedDataChildren = new String[]{
            'start','end','today','deviceid','subscriberid','simid',
            'location','phonenumber','SectionOne','closemessage'};
        dom.XmlNode[] dataChildren = data.getChildElements();
        Integer i=0;
        for(dom.XmlNode child : dataChildren)
            system.assertEquals(expectedDataChildren[i++], child.getName());

        // Assert section one contains all questions it's supposed to contain
        String[] expectedSectionOneChildren = new String[]{
            'questionSource','dateQuestion','numberQuestion','checkboxQuestion',
            'radioQuestion','questionParent','longQuestion','dateTimeQuestion',
            'maxQuestion','minQuestion','static'};
        dom.XmlNode sectionOne = data.getChildElement('SectionOne',null);
        dom.XmlNode[] sectionOneChildren = sectionOne.getChildElements();
        i=0;
        for(dom.XmlNode child : sectionOneChildren)
            system.assertEquals(expectedSectionOneChildren[i++], child.getName());

        // Assert bindings are there
        Map<String,Binding> expectedBindings = new Map<String,Binding>{
            // 'nodeset' => new Binding(type, jrPreload, jrPreloadParams, required, readonly, jrConstraint, jrConstraintMsg),
            '/data/start' => new Binding('dateTime', 'timestamp', 'start', null, null, null, null),
            '/data/end' => new Binding('dateTime', 'timestamp', 'end', null, null, null, null),
            '/data/today' => new Binding('date', 'date', 'today', null, null, null, null),
            '/data/deviceid' => new Binding('string', 'property', 'devicesid', null, null, null, null),
            '/data/subscriberid' => new Binding('string', null, null, 'true()', null, null, null),
            '/data/simid' => new Binding('string', 'property', 'simserial', null, null, null, null),
            '/data/location' => new Binding('geopoint', null, null, null, null, null, null),
            '/data/phonenumber' => new Binding('string', 'property', 'phonenumber', null, null, null, null),
            '/data/SectionOne/questionSource' => new Binding('string', null, null, 'true()', null, 'regex(.,\'^[\\w\\W]{0,50}$\')', 'Invalid response: the response exceeds 50 characters.'),
            '/data/SectionOne/dateQuestion' => new Binding('date', null, null, null, null, null, null),
            '/data/SectionOne/numberQuestion' => new Binding('int', null, null, 'true()', null, '(.>=0 and .<=10)', 'Must be between 0 and 10.'),
            '/data/SectionOne/checkboxQuestion' => new Binding('select', null, null, null, null, null, null),
            '/data/SectionOne/radioQuestion' => new Binding('select1', null, null, 'true()', null, null, null),
            '/data/SectionOne/questionParent' => new Binding('string', null, null, 'true()', null, 'regex(.,\'^[\\w\\W]{0,50}$\')', 'Invalid response: the response exceeds 50 characters.'),
            '/data/SectionOne/longQuestion' => new Binding('string', null, null, null, null, null, null),
            '/data/SectionOne/dateTimeQuestion' => new Binding('dateTime', null, null, null, null, null, null),
            '/data/SectionOne/maxQuestion' => new Binding('int', null, null, null, null, '.<=5', 'Must not be greater than 5.'),
            '/data/SectionOne/minQuestion' => new Binding('int', null, null, null, null, '.>=5', 'Must not be lower than 5.'),
            '/data/SectionOne/static' => new Binding('string', null, null, null, 'true()', null, null),
            '/data/closemessage' => new Binding('string', null, null, null, 'true()', null, null)
        };
        Integer nrOfBindsFound = 0;
        for(dom.XmlNode node : model.getChildElements()){
            if(node.getName() != 'bind') continue;
            nrOfBindsFound++;
            String nodeset = node.getAttribute('nodeset',null);
            Binding expected = expectedBindings.get(nodeset);
            system.assertEquals(expected.type, node.getAttribute('type',null), 'nodeset: ' + nodeset);
            system.assertEquals(expected.jrPreload, node.getAttribute('jr:preload',null), 'nodeset: ' + nodeset);
            system.assertEquals(expected.jrPreloadParams, node.getAttribute('jr:preloadParams',null), 'nodeset: ' + nodeset);
            system.assertEquals(expected.required, node.getAttribute('required',null), 'nodeset: ' + nodeset);
            system.assertEquals(expected.readonly, node.getAttribute('readonly',null), 'nodeset: ' + nodeset);
            system.assertEquals(expected.jrConstraint, node.getAttribute('jr:constraint',null), 'nodeset: ' + nodeset);
            system.assertEquals(expected.jrConstraintMsg, node.getAttribute('jr:constraintMsg',null), 'nodeset: ' + nodeset);
        }
        system.assertEquals(expectedBindings.keyset().size(), nrOfBindsFound);

        // Assert body exists and questions are on the right order and have the right text
        dom.XmlNode body = root.getChildElement('h:body',null);
        dom.XmlNode[] bodyChildren = body.getChildElements();
        system.assertEquals(3, bodyChildren.size()); // interviewee section, SectionOne, and close message.

        dom.XmlNode groupSuscriber = bodyChildren[0];
        system.assertEquals('field-list', groupSuscriber.getAttribute('appearance',null));
        system.assertEquals(null, groupSuscriber.getChildElement('label',null));

        dom.XmlNode[] groupSuscriberChildren = groupSuscriber.getChildElements();
        system.assertEquals(2, groupSuscriberChildren.size());
        system.assertEquals('Who are you interviewing?', groupSuscriberChildren[0].getChildElement('label',null).getText());
        system.assertEquals('Please enter the interviewee\'s Id', groupSuscriberChildren[0].getChildElement('hint',null).getText());
        system.assertEquals('GPS Location', groupSuscriberChildren[1].getChildElement('label',null).getText());
        system.assertEquals('This will capture your current location', groupSuscriberChildren[1].getChildElement('hint',null).getText());

        // Correct ordering of questions and sections is tested in another method

        // Assert the close message is correctly generated
        system.assertEquals(CLOSE_MESSAGE, bodyChildren[2].getChildElement('hint',null).getText());
        system.assertEquals(null, bodyChildren[2].getChildElement('label',null));
    }
    static testMethod void testGpsCombination(){
        //survey with no map
        Survey__c survey = new Survey__c(Name='name',Close_Message__c=null,Gps_Location_Enabled__c=false,Gps_ShowMap__c=false,InterviewMapField__c = 'Name');
        //survey with GPS enabled without map
        Survey__c survey2 = new Survey__c(Name='name',Close_Message__c=null,Gps_Location_Enabled__c=true,Gps_ShowMap__c=false,InterviewMapField__c = 'Name');
        //survey with GPS and Show Map enabled
        Survey__c survey3 = new Survey__c(Name='name',Close_Message__c=null,Gps_Location_Enabled__c=true,Gps_ShowMap__c=true,InterviewMapField__c = 'Name');
        insert (new List<Survey__c>{survey,survey2,survey3});


        Question__c sectionS1 = new Question__c(Survey__c=survey.Id,Name='s1',Caption__c='s1',Type__c=Questiontype.QUESTION_SECTION,Position__c=1);
        Question__c sectionS2 = new Question__c(Survey__c=survey2.Id,Name='s1',Caption__c='s1',Type__c=Questiontype.QUESTION_SECTION,Position__c=1);
        Question__c sectionS3 = new Question__c(Survey__c=survey3.Id,Name='s1',Caption__c='s1',Type__c=Questiontype.QUESTION_SECTION,Position__c=1);
        insert (new List<Question__c>{sectionS1,sectionS2,sectionS3});
        Question__c questionS1 = new Question__c(Survey__c=survey.Id,Name='q1',Caption__c='q1',Type__c=QuestionType.QUESTION_TEXT_LONG,Position__c=1,Parent__c=sectionS1.Id);
        Question__c questionS2 = new Question__c(Survey__c=survey2.Id,Name='q1',Caption__c='q1',Type__c=QuestionType.QUESTION_TEXT_LONG,Position__c=1,Parent__c=sectionS2.Id);
        Question__c questionS3 = new Question__c(Survey__c=survey3.Id,Name='q1',Caption__c='q1',Type__c=QuestionType.QUESTION_TEXT_LONG,Position__c=1,Parent__c=sectionS3.Id);
        insert (new List<Question__c>{questionS1,questionS2,questionS3});

        //generate the surveys xforms
        XFormCreator creatorS1 = new XFormCreator();
        dom.Document xformS1 = creatorS1.getXFormFromSurvey(survey,survey.name);
        XFormCreator creatorS2 = new XFormCreator();
        dom.Document xformS2 = creatorS2.getXFormFromSurvey(survey2,survey2.name);
        XFormCreator creatorS3 = new XFormCreator();
        dom.Document xformS3 = creatorS3.getXFormFromSurvey(survey3,survey3.name);



        dom.XmlNode rootS1 = xformS1.getRootElement();
        dom.XmlNode bodyS1 = rootS1.getChildElement('h:body',null);

        dom.XmlNode rootS2 = xformS2.getRootElement();
        dom.XmlNode bodyS2 = rootS2.getChildElement('h:body',null);

        dom.XmlNode rootS3 = xformS3.getRootElement();
        dom.XmlNode bodyS3 = rootS3.getChildElement('h:body',null);

        //S1 survey must have only one question in the standard question group
        system.assertEquals(1,bodyS1.getChildElement('group',null).getChildElements().size());
        //S2 survey must have 2 questions in the standard question group but no appearence groups
        system.assertEquals(2,bodyS2.getChildElement('group',null).getChildElements().size());
        system.assertNotEquals('maps',bodyS2.getChildElement('group',null).getChildElements()[1].getAttribute('appearance',null));
        //S3 survey must have 2 questions in the standard question group with appearence groups
        system.assertEquals(2,bodyS3.getChildElement('group',null).getChildElements().size());
        system.assertEquals('maps',bodyS3.getChildElement('group',null).getChildElements()[1].getAttribute('appearance',null));




    //survey with GPS and Show Map enabled



    }
    static testMethod void testQuestionOrdering(){
        Survey__c survey = new Survey__c(Name='name', InterviewMapField__c='LastName', Close_Message__c=null); insert survey;

        // Insert questions and sections in a randomized order, but with a correct position set
        Question__c section1 = new Question__c(Survey__c=survey.Id,Name='s1',Caption__c='s1',Type__c=Questiontype.QUESTION_SECTION,Position__c=1);
        Question__c section2 = new Question__c(Survey__c=survey.Id,Name='s2',Caption__c='s2',Type__c=Questiontype.QUESTION_SECTION,Position__c=2);
        Question__c section3 = new Question__c(Survey__c=survey.Id,Name='s3',Caption__c='s3',Type__c=Questiontype.QUESTION_SECTION,Position__c=3);
        insert new Question__c[]{section1, section3, section2};

        Question__c q1 = new Question__c(Survey__c=survey.Id,Name='q1',Caption__c='q1',Type__c=QuestionType.QUESTION_TEXT_LONG,Position__c=1,Parent__c=section1.Id);
        Question__c q2 = new Question__c(Survey__c=survey.Id,Name='q2',Caption__c='q2',Type__c=QuestionType.QUESTION_TEXT_LONG,Position__c=2,Parent__c=section1.Id);
        Question__c q3 = new Question__c(Survey__c=survey.Id,Name='q3',Caption__c='q3',Type__c=QuestionType.QUESTION_TEXT_LONG,Position__c=3,Parent__c=section1.Id);
        Question__c q4 = new Question__c(Survey__c=survey.Id,Name='q4',Caption__c='q4',Type__c=QuestionType.QUESTION_TEXT_LONG,Position__c=4,Parent__c=section2.Id);
        Question__c q5 = new Question__c(Survey__c=survey.Id,Name='q5',Caption__c='q5',Type__c=QuestionType.QUESTION_TEXT_LONG,Position__c=5,Parent__c=section2.Id);
        Question__c q6 = new Question__c(Survey__c=survey.Id,Name='q6',Caption__c='q6',Type__c=QuestionType.QUESTION_TEXT_LONG,Position__c=6,Parent__c=section2.Id);
        Question__c q7 = new Question__c(Survey__c=survey.Id,Name='q7',Caption__c='q7',Type__c=QuestionType.QUESTION_TEXT_LONG,Position__c=7,Parent__c=section3.Id);
        Question__c q8 = new Question__c(Survey__c=survey.Id,Name='q8',Caption__c='q8',Type__c=QuestionType.QUESTION_TEXT_LONG,Position__c=8,Parent__c=section3.Id);
        Question__c q9 = new Question__c(Survey__c=survey.Id,Name='q9',Caption__c='q9',Type__c=QuestionType.QUESTION_TEXT_LONG,Position__c=9,Parent__c=section3.Id);
        insert new Question__c[]{q5,q2,q7,q1,q3,q8,q6,q9,q4};

        XFormCreator creator = new XFormCreator();
        dom.Document xform = creator.getXFormFromSurvey(survey,survey.name);

        dom.XmlNode root = xform.getRootElement();
        dom.XmlNode body = root.getChildElement('h:body',null);
        dom.XmlNode[] sectionNodes = body.getChildElements();

        // Sections are elements 1 through 3
        system.assertEquals(4, sectionNodes.size());
        system.assertEquals('s1', sectionNodes[1].getChildElement('label',null).getText());
        system.assertEquals('s2', sectionNodes[2].getChildElement('label',null).getText());
        system.assertEquals('s3', sectionNodes[3].getChildElement('label',null).getText());

        // Assert questions are correctly ordered
        // Element 0 of each section is it's label
        system.assertEquals('q1', sectionNodes[1].getChildElements()[1].getChildElement('label',null).getText());
        system.assertEquals('q2', sectionNodes[1].getChildElements()[2].getChildElement('label',null).getText());
        system.assertEquals('q3', sectionNodes[1].getChildElements()[3].getChildElement('label',null).getText());
        system.assertEquals('q4', sectionNodes[2].getChildElements()[1].getChildElement('label',null).getText());
        system.assertEquals('q5', sectionNodes[2].getChildElements()[2].getChildElement('label',null).getText());
        system.assertEquals('q6', sectionNodes[2].getChildElements()[3].getChildElement('label',null).getText());
        system.assertEquals('q7', sectionNodes[3].getChildElements()[1].getChildElement('label',null).getText());
        system.assertEquals('q8', sectionNodes[3].getChildElements()[2].getChildElement('label',null).getText());
        system.assertEquals('q9', sectionNodes[3].getChildElements()[3].getChildElement('label',null).getText());
    }

    static testMethod void testListAllSurveys(){
    //PREPARING THE TEST
        Survey__c survey = new Survey__c(Name='__test__survey', Status__c = 'Published',InterviewMapField__c = 'Name');
        Survey__c survey2 = new Survey__c(Name = '__test__survey2', Status__c = 'Published',InterviewMapField__c = 'Name');
        insert new Survey__c[]{survey, survey2};
        survey  = [Select Name, Id From Survey__c WHERE Id = :survey.Id];
        survey2 = [Select Name, Id From Survey__c WHERE Id = :survey2.Id];
        SurveyVersion__c version = [SELECT Status__c, Version__c
                                    FROM SurveyVersion__c
                                    WHERE Survey__c = :survey.Id ORDER BY Version__c DESC LIMIT 1];
        version.Status__c = 'Published';
        update version;
        final String ORIGIN_URL = 'http://www.demopage.com/';
        //Contact and Contact Group Data(to filter the surveys)
        Contact con = new Contact(LastName = 'Robert');
        insert con;
        ContactGroup__c conGroup = new ContactGroup__c(Name = '__test__Group1', Status__c = 'Active');
        insert conGroup;
        ContactGroupMember__c member = new ContactGroupMember__c(Contact__c = con.Id, ContactGroup__c = conGroup.Id);
        insert member;
        ContactGroupSurveyAssignment__c surveyAssignment = new ContactGroupSurveyAssignment__c(ContactGroup__c = conGroup.Id, Survey__c = survey.Id);
        insert surveyAssignment;

        //PERFORMING THE TEST
        XFormCreator creator = new XFormCreator();

        dom.Document doc = creator.getXmlAllSurveys(ORIGIN_URL, con.Id);
        dom.XmlNode root = doc.getRootElement();
        dom.XmlNode xformNode = root.getChildElement('xform', null);
        dom.XmlNode formIdNode = xformNode.getChildElement('formID', null);
        dom.XmlNode nameNode = xformNode.getChildElement('name', null);
        dom.XmlNode versionNode = xformNode.getChildElement('version', null);
        dom.XmlNode downloadUrlNode = xformNode.getChildElement('downloadUrl', null);

        //ASSERTIONS
        System.assertEquals(survey.id, formIdNode.getText());
        System.assertEquals(survey.Name, nameNode.getText());
        System.assertEquals(version.Version__c, Integer.valueOf(versionNode.getText()));
        System.assertEquals(Page.formList.getUrl() + '?id=' + survey.Id, downloadUrlNode.getText());
    }
    /**
     * Test Listall surveys assign to a private contact.
     */
    static testMethod void testListAllSurveysPrivateContact(){
    //PREPARING THE TEST
        Profile standardProfile = [SELECT Id FROM Profile WHERE Name = 'Standard User' limit 1];
        User standardUser =  new User(
            UserName = 'test-user@test-company.com',
            FirstName = 'Test-First-Name',
            LastName = 'Test-Last-Name',
            Alias = 'test',
            Email = 'test-user@test-company.com',
            EmailEncodingKey = 'UTF-8',
            LanguageLocaleKey = 'en_US',
            LocalesIdKey = 'en_US',
            TimezonesIdKey = 'America/Los_Angeles',
            ProfileId = standardProfile.Id
            );
        insert standardUser;
        Survey__c survey = new Survey__c(Name='__test__survey', Status__c = 'Published',InterviewMapField__c = 'Name');
        Survey__c survey2 = new Survey__c(Name = '__test__survey2', Status__c = 'Published',InterviewMapField__c = 'Name');
        insert new Survey__c[]{survey, survey2};
        survey  = [Select Name, Id From Survey__c WHERE Id = :survey.Id];
        survey2 = [Select Name, Id From Survey__c WHERE Id = :survey2.Id];
        SurveyVersion__c version = [SELECT Status__c, Version__c
                                    FROM SurveyVersion__c
                                    WHERE Survey__c = :survey.Id ORDER BY Version__c DESC LIMIT 1];
        version.Status__c = 'Published';
        update version;
        final String ORIGIN_URL = 'http://www.demopage.com/';
        //Contact and Contact Group Data(to filter the surveys)
        Contact con = new Contact(LastName = 'Robert');
        insert con;
        ContactGroup__c conGroup = new ContactGroup__c(Name = '__test__Group1', Status__c = 'Active');
        insert conGroup;
        ContactGroupMember__c member = new ContactGroupMember__c(Contact__c = con.Id, ContactGroup__c = conGroup.Id);
        insert member;
        ContactGroupSurveyAssignment__c surveyAssignment = new ContactGroupSurveyAssignment__c(ContactGroup__c = conGroup.Id, Survey__c = survey.Id);
        insert surveyAssignment;

        //PERFORMING THE TEST
        //Event that i run all the test with a user which isnt able to see the private contact, Everything should work allright.
        system.runAs(standardUser){
            XFormCreator creator = new XFormCreator();

            dom.Document doc = creator.getXmlAllSurveys(ORIGIN_URL, con.Id);
            dom.XmlNode root = doc.getRootElement();
            dom.XmlNode xformNode = root.getChildElement('xform', null);
            dom.XmlNode formIdNode = xformNode.getChildElement('formID', null);
            dom.XmlNode nameNode = xformNode.getChildElement('name', null);
            dom.XmlNode versionNode = xformNode.getChildElement('version', null);
            dom.XmlNode downloadUrlNode = xformNode.getChildElement('downloadUrl', null);

            //ASSERTIONS
            System.assertEquals(survey.id, formIdNode.getText());
            System.assertEquals(survey.Name, nameNode.getText());
            System.assertEquals(version.Version__c, Integer.valueOf(versionNode.getText()));
            System.assertEquals(Page.formList.getUrl() + '?id=' + survey.Id, downloadUrlNode.getText());
        }
    }


    // Test different combinations of the first page: with/without interviewee, with/without GPS
    static testMethod void testInitialSection(){
        Survey__c survey_00 = new Survey__c(Name='s01', Alias__c=null, Status__c='Draft', InterviewMapField__c = null, Gps_Location_Enabled__c = false, Close_Message__c = null);
        Survey__c survey_01 = new Survey__c(Name='s01', Alias__c=null, Status__c='Draft', InterviewMapField__c = null, Gps_Location_Enabled__c = true, Close_Message__c = null);
        Survey__c survey_10 = new Survey__c(Name='s01', Alias__c=null, Status__c='Draft', InterviewMapField__c = 'LastName', Gps_Location_Enabled__c = false, Close_Message__c = null);
        Survey__c survey_11 = new Survey__c(Name='s01', Alias__c=null, Status__c='Draft', InterviewMapField__c = 'LastName', Gps_Location_Enabled__c = true, Close_Message__c = null);
        insert new Survey__c[]{survey_00,survey_01,survey_10,survey_11};

        XFormCreator x = new XFormCreator();
        dom.Document xform = null;
        dom.XmlNode body = null;
        dom.XmlNode groupNode = null;

        // Neither field: don't give me the group (it would be empty)
        xform = x.getXFormFromSurvey(survey_00,'s00');
        body = xform.getRootElement().getChildElement('h:body',null);
        system.assertEquals(0, body.getChildElements().size());

        // Only GPS
        xform = x.getXFormFromSurvey(survey_01,'s01');
        body = xform.getRootElement().getChildElement('h:body',null);
        system.assertEquals(1, body.getChildElements().size());

        groupNode = body.getChildElements()[0];
        system.assertEquals('group',groupNode.getName());
        system.assertEquals(1, groupNode.getChildElements().size());
        system.assertEquals('/data/location', groupNode.getChildElements()[0].getAttribute('ref',null));

        // Only Interviewee
        xform = x.getXFormFromSurvey(survey_10,'s10');
        body = xform.getRootElement().getChildElement('h:body',null);
        system.assertEquals(1, body.getChildElements().size());

        groupNode = body.getChildElements()[0];
        system.assertEquals('group',groupNode.getName());
        system.assertEquals(1, groupNode.getChildElements().size());
        system.assertEquals('/data/subscriberid', groupNode.getChildElements()[0].getAttribute('ref',null));

        // Both Interviewee and GPS. They must appear in that order.
        xform = x.getXFormFromSurvey(survey_11,'s11');
        body = xform.getRootElement().getChildElement('h:body',null);
        system.assertEquals(1, body.getChildElements().size());

        groupNode = body.getChildElements()[0];
        system.assertEquals('group',groupNode.getName());
        system.assertEquals(2, groupNode.getChildElements().size());
        system.assertEquals('/data/subscriberid', groupNode.getChildElements()[0].getAttribute('ref',null));
        system.assertEquals('/data/location', groupNode.getChildElements()[1].getAttribute('ref',null));
    }
}
