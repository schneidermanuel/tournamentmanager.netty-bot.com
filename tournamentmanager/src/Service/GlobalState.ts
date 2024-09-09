import { Injectable } from "@angular/core"; import { User } from "../Data/User";

@Injectable({
  providedIn: 'root'
})
export class GlobalState {
  public IsInited: boolean = false;
  public User: User;
}
