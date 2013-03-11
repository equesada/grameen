public with sharing class C {

    //EXCEPTION CLASS FOR PPI LICENSE
    public class LicenseException extends Exception {}
    
    //CREATE INSTANCE OF LICENSE IF IT DOESN'T EXIST
    public static LicenseSettings__c getLicense(){
        String licenseName = Test.isRunningTest() ? 'Test' : 'Default';
        LicenseSettings__c license = LicenseSettings__c.getInstance(licenseName);
        if (license == null){
            license = new LicenseSettings__c(Name=licenseName);
            insert license;
        }
        return license;
    }


    // Wait the indicated number of questions.
    // WARNING: this performs busy waiting, which executes lots of statements per second. Beware governor limits.
    public static void wait(Integer seconds){
        Long l = 0;
        DateTime finishTime = DateTime.now().addSeconds(seconds);
        while(finishTime > DateTime.now()){
            l = (l - l) + Crypto.getRandomLong(); // weightly calculation. Takes time, but counts as one statement.
        }
    }
}