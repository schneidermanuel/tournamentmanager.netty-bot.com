import { Injectable } from "@angular/core";
import { Api } from "./Api";

@Injectable({
  providedIn: 'root'
})
export class TournamentService {
  constructor(private api: Api) { }

  public async GetTournaments() {
    let result = await this.api.SendGetRequest("Tournaments/list");
    console.log(result);
  }
}
