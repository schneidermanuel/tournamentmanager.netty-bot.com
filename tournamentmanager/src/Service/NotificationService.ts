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
}
