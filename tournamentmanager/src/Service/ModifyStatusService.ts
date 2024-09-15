import { Injectable } from "@angular/core";
import { ModifyStatusAction } from "./ModifyStatusAction";

@Injectable({
  providedIn: 'root'
})
export class ModifyStatusService {

  public GetValidActions(currentStatus: string): ModifyStatusAction[] {
    let resultSet: ModifyStatusAction[] = [];
    if (currentStatus == "preparation") {
      resultSet.push(new ModifyStatusAction("Open", "join"));
    }
    if (currentStatus == "join") {
      resultSet.push(new ModifyStatusAction("Complete Registration", "closed"));
      resultSet.push(new ModifyStatusAction("Reset", "preparation"));
    }

    if (currentStatus == "closed") {
      resultSet.push(new ModifyStatusAction("Re-Open", "join"));
      resultSet.push(new ModifyStatusAction("Finalize", "completed"));
    }

    return resultSet;
  }
}
