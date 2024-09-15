export class ModifyStatusAction {
  public ActionName: string;
  public NewStatus: string;
  constructor(actionName: string, newStatus: string) {
    this.ActionName = actionName;
    this.NewStatus = newStatus;
  }
}
