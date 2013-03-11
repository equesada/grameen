public class SurveyException extends Exception{

    public static void raise(String msg) {
        throw new SurveyException(msg);    
    }
    
    public static void raise(String msg, String arg) {
        throw new SurveyException(String.format(msg, new String[]{arg}));
    }
    
    public static void raise(String msg, String[] args) {
    	throw new SurveyException(String.format(msg, args));
    }

}