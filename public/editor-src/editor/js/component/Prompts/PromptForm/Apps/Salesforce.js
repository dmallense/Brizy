import { ApiConnect, Account, Fields, List, Done } from "../Step";

class Salesforce {
  static connect = ApiConnect;
  static account = Account;
  static fields = Fields;
  static list = List;
  static done = Done;
}

export default Salesforce;
