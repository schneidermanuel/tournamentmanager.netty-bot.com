import { EventEmitter, Injectable } from "@angular/core";
import { EventBar } from "../Data/EventBar";

@Injectable({
  providedIn: "root"
})
export class NotificationService {
  public EventSource: EventEmitter<EventBar>;
  constructor() {
    this.EventSource = new EventEmitter<EventBar>
  }

  public ShowError(text: string) {
    var event = new EventBar("E", text);
    this.EventSource.emit(event);
  }
  public ShowWarnung(text: string) {
    var event = new EventBar("W", text);
    this.EventSource.emit(event);
  }
  public ShowMessage(text: string) {
    var event = new EventBar("I", text);
    this.EventSource.emit(event);
  }
}
