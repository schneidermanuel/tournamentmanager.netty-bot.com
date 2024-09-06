import { Injectable } from "@angular/core";
import { Authenticator } from "./Authenticator";

@Injectable({
  providedIn: 'root'
})
export class Api {
  private _backendUrl = "https://api.tournamentmanager.netty-bot.com/";
  constructor(private authenticator: Authenticator) { }

  public async SendGetRequest(location: String): Promise<any> {
    await fetch(this._backendUrl + location)
      .then(data => data.json())
  }
}
