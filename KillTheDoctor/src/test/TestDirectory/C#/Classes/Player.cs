using UnityEngine;
using System.Collections;

namespace Game
{
	[System.Serializable]
	public class Player  {

		private PhotonPlayer player;
		private PhotonPlayer enemy;
		private Player possibleEnemyOne;
		private Player possibleEnemyTwo;
		private int winCount;
		private bool isReady;
		private int currentHand;
		private bool isDefeated;
		private bool isWaiting;
		private int playerIndex;

		public Player PossibleEnemyOne {

			get { return possibleEnemyOne; }
			set { possibleEnemyOne = value; }

		}

		public Player PossibleEnemyTwo {

			get { return possibleEnemyTwo; }
			set { possibleEnemyTwo = value; }
		}

		public int PlayerIndex {

			get { return playerIndex; }
			set { playerIndex = value; }
		}

		public bool IsWaiting
		{

			get { return isWaiting; }
			set { isWaiting = value; }

		}

		public bool IsDefeated 
		{

			get { return isDefeated; }
			set { isDefeated = value; }
		}

		public int CurrentHand
		{

			get{ return currentHand;  }
			set{ currentHand = value; }

		}

		public bool IsReady
		{

			get{ return isReady; }
			set{ isReady = value; }

		}

		public int WinCount
		{

			get{ return winCount; }
			set{ winCount = value; }

		}

		public string PlayerName
		{

			get{ return player.name; }

		}

		public PhotonPlayer PhotonPlayer
		{

			get{ return player; }

		}

		public PhotonPlayer PhotonPlayerEnemy
		{

			get{ return enemy; }
			set{ enemy = value; }

		}

		public Player()
		{


		}

		public Player(PhotonPlayer player)
		{

			this.player = player;

		}

	}
}