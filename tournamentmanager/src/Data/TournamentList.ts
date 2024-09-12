import { Tournament } from "./Tournament";

export class TournamentList {
  public OpenTournaments: Tournament[];
  public ClosedTournaments: Tournament[];
  public OtherTournaments: Tournament[];

  constructor() {
    this.OpenTournaments = [];
    this.ClosedTournaments = [];
    this.OtherTournaments = [];
  }
}
