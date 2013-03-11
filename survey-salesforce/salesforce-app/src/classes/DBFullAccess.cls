public without sharing class DBFullAccess {
    public static void DeleteSObject(SObject objectToDelete){
        delete objectToDelete;
    }
    public static void DeleteSObject(List<SObject> objectsToDelete){
        delete objectsToDelete;
    }
    public static void InsertSObject(SObject objectToInsert){
        insert objectToInsert;
    }
    public static void InsertSObject(List<SObject> objectsToInsert){
        insert objectsToInsert;
    }
    public static void UpdateSObject(SObject objectToUpdate){
        update objectToUpdate;
    }
    public static void UpdateSObject(List<SObject> objectsToUpdate){
        update objectsToUpdate;
    }
    public static void UpsertSObject(SObject objectToUpsert){
        upsert objectToUpsert;
    }
    public static void UpsertSObject(List<SObject> objectsToUpsert){
        upsert objectsToUpsert;
    }
    public static List<SObject> QuerySObject(String queryString){
        return DataBase.query(queryString);
    }
}