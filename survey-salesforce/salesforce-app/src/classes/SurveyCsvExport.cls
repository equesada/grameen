public with sharing class SurveyCsvExport {

    private static final String NO_DATA_MSG='No data available for this survey';
    private static final String HEADER_SUBMISSION_DATA= '"startDate","endDate","Date","Device","gps_x","gps_y","Interviewee","Surveyor",';
    private static final String ERR_EXPECTED_TEXT_FOUND_ELEMENT = 'Error parsing submission XML: Expected text node, found element';
    public String surveyId {get;set;}
    /*class that represents a Csv File ,used to manage
    the construction of a CSV file*/

    public class csvFile{
        public String headerRow;
        public List<String> columns= new List<String>();
        public List<String> scoringGroupsColumns = new List<String>();
        public Map<Id,String> answerRow = new Map<Id,String>();

        //set the header row for the csv file,only need to be called once
        public void setHeader(List<Question__c> surveyQuestions,String SubmissionFields){
            headerRow = SubmissionFields;
            // Questions query does not fetch sections and static questions, so I know all questions are to be output.
            for(Question__c question:surveyQuestions){
                headerRow += '"' +  question.Name + '",';
                Columns.add(question.Name);
            }
        }

        //set a Row for a Submission with their answers
        //Precondition: setHeader must be previosly called to generate Columns.
        public void setRow(Submission__c submission,Map<String,String> AnswerByQuestionName){
            //create a String variable that keeps the csv row
            String rowString = '"'
                + submission.startDate__c + '","'
                + submission.endDate__c + '","'
                + submission.Date__c + '","'
                + submission.Device__c +'","'
                + (submission.gps_x__c != null ? String.valueOf(submission.gps_x__c) : '') + '","'
                + (submission.gps_y__c != null ? String.valueOf(submission.gps_y__c) : '') + '","'
                + (submission.interviewee__c != null ? submission.Interviewee__r.Name : '') + '","'
                + submission.Surveyor__r.Name +'",';
            for (String col:Columns){
                rowString += '"' + (AnswerByQuestionName.get(col) != null ? AnswerByQuestionName.get(col) : '') + '",';
            }
            AnswerRow.put(Submission.Id,rowString);
        }
        //Add the score data in a submission row
        public void setRowScores(Submission__c submission,Map<String,SubmissionScore__c> scoreByScoreGroup){

            for (String col:scoringGroupsColumns){
                answerRow.put(
                    submission.Id,
                    answerRow.get(submission.Id) + '"' + (scoreByScoreGroup.get(col)!=null ? String.valueOf(scoreByScoreGroup.get(col).TotalScore__c) : '') + '",');
            }

        }

        public String getString(){
            String csv = headerRow + '\n';
            for (String row:answerRow.values()){
                csv += row +'\n';
            }
            return csv;
        }

        public void setScoringGroupsColumns(List<ScoringGroup__c> scoringGroups){
            for(ScoringGroup__c sg:scoringGroups){
                    headerRow += '"'+ sg.Caption__c + '",';
                    scoringGroupsColumns.add(sg.Caption__c);
    }
        }

    }


    public String getCsvString(){
        surveyId = ApexPages.currentPage().getParameters().get('surveyid');
        String stringSubmissions = ApexPages.currentPage().getParameters().get('selectedSurveysAnswerId');
        List<Submission__c> submissionsToExport;

        if (stringSubmissions!=null && stringSubmissions!=''){

            List<String> answerIdsParam = stringSubmissions.split(',',0);
            submissionsToExport =  [select Id,survey__c,
                                            startDate__c ,
                                            endDate__c,
                                            Date__c,
                                            Device__c,
                                            gps_x__c,
                                            gps_y__c,
                                            surveyor__c, surveyor__r.Name,
                                            interviewee__c, interviewee__r.Name
                                            from Submission__c where Id in :answerIdsParam];
            surveyId = (SubmissionsToExport.size()>0)? SubmissionsToExport[0].survey__c: null;

        }else if (surveyId!=null){
            submissionsToExport = [select Id,survey__c,
                                            startDate__c ,
                                            endDate__c,
                                            Date__c,
                                            Device__c,
                                            gps_x__c,
                                            gps_y__c,
                                            surveyor__c, surveyor__r.Name,
                                            interviewee__c, interviewee__r.Name
                                            from Submission__c where survey__c = :surveyId];
        }
        return exportToCsv(surveyId,SubmissionsToExport);
    }

    //Method to convert Survey answer xml to a CSV file for further export
    public String exportToCsv(Id surveyId,Submission__c[] submissionsToExport){
        //Survey Questions and responses
        List<Answer__c> surveyAnswers = [
            SELECT Id,Value__c,Question__r.Name,Option__r.Name,Option__r.Caption__c,Question__r.Type__c,Submission__c
            FROM Answer__c
            WHERE Submission__c IN :submissionsToExport
            ORDER BY Submission__c, Question__r.Parent__r.Position__c, Question__r.Position__c, Option__r.Position__c];
        List<Question__c> surveyQuestions = [
            SELECT Id,Name,Caption__c,Type__c
            FROM Question__c
            WHERE Survey__c = :surveyId AND Type__c NOT IN (:QuestionType.QUESTION_SECTION, :QuestionType.QUESTION_STATIC)
            ORDER BY Parent__r.Position__c, Position__c];
        //survey Scoring
        List<ScoringGroup__c> scoringGroups = [SELECT Id, Caption__c FROM ScoringGroup__c WHERE survey__c = :surveyId ORDER BY Id];

        // Save a map of Submission Id -> Scoring Group Id -> Score
        List<submissionScore__c> scores = [SELECT Id, TotalScore__c,Submission__c,ScoringGroup__c,ScoringGroup__r.Caption__c
                                           FROM SubmissionScore__c
                                           WHERE Submission__c in :SubmissionsToExport
                                           ORDER BY Submission__c, ScoringGroup__c];


        csvFile csv = new CsvFile();
        //generate the header row of the csv

        csv.setHeader(surveyQuestions,HEADER_SUBMISSION_DATA);
        csv.setScoringGroupsColumns(scoringGroups);
        //generate each submission row
        for (Submission__c sub:submissionsToExport){
            Map<String,String> AnswerByQuestionName = new Map<String,String>();
            Map<String,SubmissionScore__c> ScoreByScoreGroup = new Map<String,SubmissionScore__c>();
            //generate the map for each submission,retriving the first element of the list adding it to the map
            //and removing it,stop to loop when the first element doesn't belong to the survey

            while(!surveyAnswers.isEmpty() && surveyAnswers[0].Submission__c == sub.Id){
                //if the answer belongs to the same column separate the values with ; (checkbox case)
                AnswerByQuestionName.put(
                    surveyAnswers[0].Question__r.Name,
                    (AnswerByQuestionName.get(surveyAnswers[0].Question__r.Name) != null
                        ? AnswerByQuestionName.get(surveyAnswers[0].Question__r.Name) + ';'
                        : ''
                        ) + surveyAnswers.remove(0).Value__c);
            }

            csv.setRow(sub,AnswerByQuestionName);
            //generate the map for each scoring
            while(!scores.isEmpty() && scores[0].Submission__c == sub.Id){
                String groupName = scores[0].ScoringGroup__r.Caption__c;
                ScoreByScoreGroup.put(groupName,scores.remove(0));
            }
            csv.setRowScores(sub,ScoreByScoreGroup);
        }

        return csv.getString();
    }
}