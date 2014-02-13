using UnityEngine;
using System.Collections;
using RPS.Models;
public class DataConnection : MonoBehaviour {

	private bool isDataRendered = false;
	private User user;
	private Tournament tournament;

	// Use this for initialization
	void Start () {
		this.guiText.text = "Loading data...";
		this.user = User.GetById (2);


		this.tournament = Tournament.GetById (1, Tournament.INCLUDE_HISTORY);
	
//		this.tournament.Id = 1;
//		this.tournament.HostId = 1;
//		this.tournament.TournamentName = "Tourny updated from c# script";
//		this.tournament.NumberOfPlayersId = 1;
//		this.tournament.TreeLevel = 0;
//		this.tournament.Update ();
		TournamentHistory th = new TournamentHistory ();
		th.UserId1 = 2;
		th.UserId2 = 5;
		th.TournamentId = 1;
		th.TreeLevel = 3;
		Debug.Log ("Insert:");
		Debug.Log(th.Insert ());
	}
	
	// Update is called once per frame
	void Update () {

	}

	void OnGUI() {
		if (!this.isDataRendered) {
			if (this.tournament.IsReady()) {

				this.user.NumberOfWins += 1;
				this.user.Update();

				this.guiText.text = this.tournament.TournamentName;
				this.isDataRendered = true;
				Debug.Log(tournament.ToString());
				foreach (User user in this.tournament.Players) {
					Debug.Log(user.ToString());
				}
				foreach (TournamentHistory history in this.tournament.History) {
					Debug.Log(history.ToString());
				}
			}
		}
	}


}
