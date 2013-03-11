public with sharing class QuestionType {

    /*Question values*/

    public static final String QUESTION_TEXT_SHORT = 'text-short';
    public static final String QUESTION_TEXT_LONG = 'text-long';
    public static final String QUESTION_NUMBER = 'number';
    public static final String QUESTION_NUMBER_INTEGER = 'number-integer';
    public static final String QUESTION_NUMBER_DECIMAL = 'number-decimal';
    public static final String QUESTION_DATE = 'date-date';
    public static final String QUESTION_RADIO = 'radio';
    public static final String QUESTION_CHECKBOX = 'checkbox';
    public static final String QUESTION_DATETIME = 'date-datetime';
    public static final String QUESTION_STATIC = 'static-content';
    public static final String QUESTION_SECTION = 'section';
    public static final Map<string,string> QuestionTypeToSchemaType = new Map<string,string>{QUESTION_TEXT_LONG => Schema.Displaytype.TextArea.name(),
        QUESTION_NUMBER_INTEGER => Schema.Displaytype.Double.name(),QUESTION_NUMBER => Schema.Displaytype.Double.name(), QUESTION_NUMBER_DECIMAL => Schema.Displaytype.Double.name(), QUESTION_DATE => Schema.Displaytype.Date.name(),QUESTION_RADIO => Schema.Displaytype.Picklist.name(),
        QUESTION_CHECKBOX => Schema.Displaytype.MultiPicklist.name(),QUESTION_DATETIME => Schema.Displaytype.DateTime.name()};

    public static final Set<String> NUMERIC_TYPES = new Set<String>{
        QUESTION_NUMBER, QUESTION_NUMBER_INTEGER, QUESTION_NUMBER_DECIMAL
    };

    /*Condition Picklist values*/

    public static final String SKIPLOGIC_CONDITION_GREATER = 'GreaterThan';
    public static final String SKIPLOGIC_CONDITION_LESSER = 'LesserThan';
    public static final String SKIPLOGIC_CONDITION_EQUAL = 'Is';
    public static final String SKIPLOGIC_CONDITION_ISANSWERED = 'Answered';

    /*String Xforms types*/

    public static final String XFORMTYPE_STRING = 'string';
    public static final String XFORMTYPE_TIME = 'time';
    public static final String XFORMTYPE_DATETIME = 'dateTime';
    public static final String XFORMTYPE_DATE = 'date';
    public static final String XFORMTYPE_NUMBER = 'int';
    public static final String XFORMTYPE_NUMBER_INTEGER = 'int';
    public static final String XFORMTYPE_NUMBER_DECIMAL = 'decimal';

    /*method to get the logic for the Xform generation*/

    public static String getRelevants(SkipCondition__c sk,Map<Id,String> questionPaths){
        String relevantString='';

        if (sk.SourceQuestion__r.Type__c==QuestionType.QUESTION_RADIO || sk.SourceQuestion__r.Type__c==QuestionType.QUESTION_CHECKBOX){
            String value;
            String conditionType;
            if (sk.Condition__c==SKIPLOGIC_CONDITION_EQUAL){
                value = sk.Value__c;
                relevantString = 'selected(' + questionPaths.get(sk.SourceQuestion__c) + ',' + '\'' +value+ '\'' + ')';
                if (sk.Negate__c) relevantString = 'not(' + relevantString + ')';

            }else if (sk.Condition__c==SKIPLOGIC_CONDITION_ISANSWERED)
                relevantString = questionPaths.get(sk.SourceQuestion__c) + (sk.Negate__c?'=':'!=') + '\'\'';
            
        }else {
            String value;
            String conditionType;

            if(sk.Condition__c==SKIPLOGIC_CONDITION_ISANSWERED){
                conditionType=(sk.Negate__c)?'=':'!=';
                value ='\'\'';
            }else{
                if (sk.Condition__c==SKIPLOGIC_CONDITION_GREATER){
                    conditionType=(sk.Negate__c)?'<=':'>';
                }else if(sk.Condition__c==SKIPLOGIC_CONDITION_LESSER ){
                    conditionType=(sk.Negate__c)?'>=':'<';
                }else if(sk.Condition__c==SKIPLOGIC_CONDITION_EQUAL){
                    conditionType=(sk.Negate__c)?'!=':'=';
                }

                value = sk.Value__c;
                if (!NUMERIC_TYPES.contains(sk.SourceQuestion__r.Type__c.toLowerCase()))
                    value = '\'' + value + '\'';
            }
            
            relevantString = questionPaths.get(sk.SourceQuestion__c) + conditionType + value;
        }

        return relevantString;
    }
    // used for marking all question
    public abstract class IQuestion {
        /*add the declaration node on the model tag*/
        public abstract void getXFormsModel(dom.XmlNode xmlModel);
        /*Add the Body nodes of the xform*/
        public abstract void getXFormBody(dom.XmlNode xmlBody);
        /*add the binding node of the field */
        public abstract void getBindings(dom.XmlNode xmlBody,List<SkipCondition__c> skipConditions,Map<Id,String> questionPaths);

        private String getQuestionRelevant(List<SkipCondition__c> skipConditions,Map<Id,String> questionPaths,Id questionId,String operator,String behavior){
            String relevant = '' ;
            String xFormOperator = (operator == 'Any') ? ' or ' : ' and ';
            for (SkipCondition__c sk:skipConditions){
                if(sk.Parent__c==questionId){
                     relevant += (relevant != '' ?  xFormOperator  : '') + getRelevants(sk,questionPaths);
                }
            }
            if (relevant != null && relevant != '' && behavior == 'Hide'){
                relevant = 'not('+ relevant + ')';
            }
            return relevant;
        }
    }

    public class SimpleInput extends IQuestion{
        public String Path      { get; set; }
        public String Id      { get; set; }
        public String Name         { get; set; }
        public String Caption      { get; set; }
        public String Hints        { get; set; }
        public double Maximum      { get; set; }
        public double Minimum      { get; set; }
        public String Type         { get; set; }
        public Boolean Required    { get; set; }
        public String relevants    { get; set; }
        public String logicBehavior { get; set; }
        public String logicOperator { get; set; }
        private String typeXml     { get; set; }




        public override void getXFormsModel(dom.XmlNode xmlModel){
            xmlModel.addChildElement(this.Name.replace(' ','_'),null,null);
        }

        public override void getXFormBody(dom.XmlNode xmlBody){

            dom.Xmlnode xmlIntervieweeInput = xmlBody.addChildElement('input',null,null);
            xmlIntervieweeInput.setAttribute('ref',path.replace(' ','_')+ '/'+ Name.replace(' ','_'));
            xmlIntervieweeInput.addChildElement('label',null,null).addTextNode(this.Caption);
            xmlIntervieweeInput.addChildElement('hint',null,null).addTextNode(this.Hints);
        }

        public override void getBindings(dom.xmlNode xmlModel,List<SkipCondition__c> skipConditions,Map<Id,String> questionPaths){
            String max;
            String min;
            String constraint='';

            max =(this.Maximum==null) ? '' : this.Maximum.Format();
            min =(this.Minimum==null) ? '' : this.Minimum.Format();

            String constraintMsg;
            if (this.Type == QUESTION_STATIC) {
                this.typeXml = XFORMTYPE_STRING;
            }else if (this.Type == QUESTION_TEXT_SHORT) {
                this.typeXml = XFORMTYPE_STRING;
                constraint = 'regex(.,\'^[\\w\\W]{0,50}$\')';
                constraintMsg='Invalid response: the response exceeds 50 characters.';
            }
            else if(this.Type== QUESTION_TEXT_LONG)
                this.typeXml = XFORMTYPE_STRING;
            else if ( NUMERIC_TYPES.contains(this.Type.toLowerCase()) ){
                this.typeXml = this.Type == QUESTION_NUMBER_DECIMAL
                    ? XFORMTYPE_NUMBER_DECIMAL
                    : XFORMTYPE_NUMBER_INTEGER;

                if (max!='' && min !=''){
                    constraint = '(.>='+ min +' and .<='+ max +')';
                    constraintMsg = 'Must be between ' + min + ' and ' + max + '.';
                }
                else if(max!='' || min !=''){
                    constraint = (max!='')? '.<='+ max : '.>='+ min;
                    constraintMsg = 'Must not be ' + (max!='' ? 'greater' : 'lower') + ' than ' + (max!='' ? max : min) + '.';
                }
            }else if(this.Type == QUESTION_DATETIME)
                this.typeXml = XFORMTYPE_DATETIME;
            else if(this.Type == QUESTION_DATE)
                this.typeXml = XFORMTYPE_DATE;

            /*appy the relevant atribute for this question*/
            if (relevants == '' || relevants == null){
                relevants = getQuestionRelevant(skipConditions,questionPaths,Id,logicOperator,logicBehavior);
            }

            dom.Xmlnode xmlBind = xmlModel.addChildElement('bind',null,null);
            xmlBind.setAttribute('nodeset',path.replace(' ','_')+ '/' + Name.replace(' ','_'));
            xmlBind.setAttribute('type',this.typeXml);
        
            if (this.Required){
                xmlBind.setAttribute('required','true()');
            }
            if(this.Type == QUESTION_STATIC){
                xmlBind.setAttribute('readonly','true()');
            }            
            if (constraint!='' && constraint!=null){
                xmlBind.setAttribute('jr:constraint',constraint);
                if (constraintmsg!='' && constraintmsg!=null){
                    xmlBind.setAttribute('jr:constraintMsg',constraintmsg);
                }
            }
            if (relevants != '' && relevants != null){
                xmlBind.setAttribute('relevant',relevants);
            }
        }
    }

    public class SelectionQuestion extends IQuestion{
        public String Path      { get; set; }
        public String Id      { get; set; }
        public String Name         { get; set; }
        public String Caption      { get; set; }
        public String Hints        { get; set; }
        public String Type         { get; set; }
        public Boolean Required    { get; set; }
        public String logicBehavior { get; set; }
        public String logicOperator { get; set; }
        private String typeXml     { get; set; }

        private List<Option__c> qOptions = new List<Option__c>();

        public SelectionQuestion(String questionName,String questionType,List<Option__c> allOptions){
            this.Name = questionName;
            this.Type = questionType;
            if (Type == QUESTION_RADIO){
                typeXml='select1';
            }else if (Type == QUESTION_CHECKBOX) {
                typeXml='select';
            }
            for (Option__c opt: allOptions){
                if (opt.Question__r.Name == questionName) {
                    qOptions.add(opt);
                }
            }
        }

        public override void getXFormsModel(dom.XmlNode xmlModel){
            xmlModel.addChildElement(this.Name.replace(' ','_'),null,null);
        }
        public override void getBindings(dom.XmlNode xmlModel,List<SkipCondition__c> skipConditions,Map<Id,String> questionPaths){

            dom.XmlNode xmlBind = xmlModel.addChildElement('bind',null,null);
            xmlBind.setAttribute('nodeset',path.replace(' ','_')+ '/' + Name.replace(' ','_'));
            xmlBind.setAttribute('type',this.typeXml);
            String relevant= getQuestionRelevant(skipConditions,questionPaths,Id,logicOperator,logicBehavior);
            if (this.Required){
                xmlBind.setAttribute('required','true()');
               }
               if (relevant != ''){
                   xmlBind.setAttribute('relevant',relevant);
               }
        }
        public override void getXFormBody(dom.XmlNode xmlBody){

             //Get all the options for the Radio Question
             if (!(path=='' || path==null)){
                   path= path + '/';
              }
              dom.xmlNode xmlSelect = xmlBody.addChildElement(typeXml,null,null);
              xmlSelect.setAttribute('ref',path+ this.Name);
              xmlSelect.addChildElement('label',null,null).addTextNode(this.Caption);
              xmlSelect.addChildElement('hint',null,null).addTextNode(this.Hints);
             for (Option__c opt: qOptions){
                 dom.xmlNode xmlItem = xmlSelect.addChildElement('item',null,null);
                 xmlItem.addChildElement('label',null,null).addTextNode(opt.Caption__c);
                 xmlItem.addChildElement('value',null,null).addTextNode(opt.Id);
            }
        }
    }

    public class Section extends IQuestion{
        public String Name       { get; set; }
        public String Caption    { get; set; }
        public String Hints      { get; set; }
        public Boolean ShowInSinglePage { get; set; }
        public List<IQuestion> sectionQuestions { get; set; }

        public Section(){
            sectionQuestions= new List<IQuestion>();
        }

        public override void getXFormsModel(dom.XmlNode xmlModel){
            dom.xmlNode xmlSection = xmlModel.addChildElement(this.Name.replace(' ','_'),null,null);
            for (IQuestion q: sectionQuestions){
                    q.GetXFormsModel(xmlSection);
            }
        }
        public override void getXFormBody(dom.XmlNode xmlBody){
             String sectionBody = '';
             String questionBody = '';
             dom.Xmlnode xmlBodySection = xmlBody.addChildElement('group',null,null);
             if (ShowInSinglePage) xmlBodySection.setAttribute('appearance','field-list');
             xmlBodySection.addChildElement('label',null,null).addTextNode(this.Caption);

             for (IQuestion q: sectionQuestions){
                q.getXFormBody(xmlBodySection);
            }
       }
       public override void getBindings(dom.XmlNode xmlBody,List<SkipCondition__c> skipConditions,Map<Id,String> questionPaths){
            for (IQuestion q: sectionQuestions){
                q.getBindings(xmlBody,skipConditions,questionPaths);
            }
       }
    }
}