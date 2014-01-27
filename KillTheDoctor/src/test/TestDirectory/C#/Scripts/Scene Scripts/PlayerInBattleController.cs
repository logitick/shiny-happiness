using UnityEngine;
using System.Collections;
using System.Collections.Generic;
using Game;
using System.Linq;
using System;


public class PlayerInBattleController : Photon.MonoBehaviour 
{

	#region variables

	public Font font;

	public Sprite[] choices;
	public Sprite rock;
	public Sprite paper;
	public Sprite scissors;
	public Sprite happyFace;
	public Sprite sadFace;
	public Sprite twoThirdsLife;
	public Sprite oneThirdLife;
	public Sprite fullLife;
	public Sprite zeroLife;

	private SpriteRenderer spriteRenderer;
	private SpriteRenderer spriteRenderer2;
	private SpriteRenderer spriteRendererFace;
	private SpriteRenderer spriteRendererFace2;
	private SpriteRenderer spriteRendererLifeBar;
	private SpriteRenderer spriteRendererLifeBar2;
	private Animator anim;
	private Animator animEnemy;
	public PlayerList playerList;
	private GUIText guiTextTimer;
	private GUIText guiTextAfterRound;
	private GUIText guiTextPlayerScore;
	private GUIText guiTextPlayerTwoScore;
	private GUIText guiTextPossibleEnemies;
	private Player player;
	private Player playerEnemy;
	private Mathify mathify;

	private float roundFinishTime;
	private float roundFinishTimer;
	private float animatorTime;
	private float animatorTimer;
	private float roundTime;
	public float roundTimer;
	private int defaultChoice;
	private bool isResultRecorded;
	private bool isOver;
	
	private string[] setBoolAnimator;
	private bool isAnimationChosen;
	public int currentSetBoolAnimator;
	public string playerOne;
	public string playerTwo;
	private int playedAnimation;
	public int playerTwoIndex;
	public int winLimit;
	private int game;

	public Texture2D textureHoveredButton;
	public Texture2D textureClickedButton;
	public Texture2D textureButton;
	private GUIStyle button;

	GameObject RockObject;
	GameObject PaperObject;
	GameObject ScissorsObject;

	#endregion

	#region unity methods

	void Awake()
	{ 
		Destroy(GameObject.Find("PlayerName"));
		gameObject.name = PhotonNetwork.player.name;
		GameObject playerInfoObject = GameObject.Find("PlayerList");
		playerList = playerInfoObject.GetComponent<PlayerList>();

		RockObject = GameObject.Find("rock");
		PaperObject = GameObject.Find("paper");
		ScissorsObject = GameObject.Find("scissors");

		RockObject.GetComponent<ChoiceController>().enabled = true;
		PaperObject.GetComponent<ChoiceController>().enabled = true;
		ScissorsObject.GetComponent<ChoiceController>().enabled = true;

		RockObject.transform.GetComponent<ChoiceController>().playerName = this.gameObject.name;
		PaperObject.transform.GetComponent<ChoiceController>().playerName = this.gameObject.name;
		ScissorsObject.transform.GetComponent<ChoiceController>().playerName = this.gameObject.name;

		guiTextTimer = GameObject.Find("TimerText").guiText;
		guiTextAfterRound = GameObject.Find("AfterRoundText").guiText;
		guiTextPossibleEnemies = GameObject.Find("PossibleEnemies").guiText;

		setBoolAnimator = new string[3];
		choices = new Sprite[3];

		this.AssignPlayerToGameObject();
		anim = GameObject.Find(playerOne).transform.GetChild(2).transform.GetComponent<Animator>();
		spriteRenderer = GameObject.Find(playerOne).transform.GetChild(2).transform.GetChild(0).transform.GetChild(0).transform.GetComponent<SpriteRenderer>();
		spriteRendererFace = GameObject.Find (playerOne).transform.GetChild(0).transform.GetChild(0).transform.GetComponent<SpriteRenderer>();
		spriteRendererLifeBar = GameObject.Find(playerOne).transform.GetChild(1).transform.GetComponent<SpriteRenderer>();

		animEnemy = GameObject.Find(playerTwo).transform.GetChild(2).transform.GetComponent<Animator>();
		spriteRenderer2 = GameObject.Find(playerTwo).transform.GetChild(2).transform.GetChild(0).transform.GetChild(0).transform.GetComponent<SpriteRenderer>();
		spriteRendererFace2 = GameObject.Find (playerTwo).transform.GetChild(0).transform.GetChild(0).transform.GetComponent<SpriteRenderer>();
		spriteRendererLifeBar2 = GameObject.Find(playerTwo).transform.GetChild(1).transform.GetComponent<SpriteRenderer>();



	}


	void Start () 
	{ 
		//if(photonView.isMine)
		{

			defaultChoice = 0;
			player = playerList.GetPlayer(PhotonNetwork.playerName);
			mathify = new Mathify();
			player.CurrentHand = defaultChoice;
			setBoolAnimator[0] = "toRock";
			setBoolAnimator[1] = "toPaper";
			setBoolAnimator[2] = "toScissors";

			choices[0] = rock;
			choices[1] = paper;
			choices[2] = scissors;

			game = 1;
			winLimit = 3;
			isResultRecorded = false;
			isAnimationChosen = false;
			currentSetBoolAnimator = defaultChoice;
			animatorTime = 4.0f;
			roundTime = 11.0f;
			roundFinishTime = 5.0f; 
			roundFinishTimer = roundFinishTime;
			animatorTimer = animatorTime;
			roundTimer = roundTime;

			player.PlayerIndex = playerList.GetPlayerAtIndex(PhotonNetwork.playerName);
			this.SetPossibleEnemies();

			ResetTimer(TimerType.Round);

			guiTextPlayerScore.text = player.PlayerName;
			guiTextPlayerTwoScore.text = player.PhotonPlayerEnemy.name;



		}


	}

	void OnGUI()
	{
		if(photonView.isMine && isOver)
		{
			button = new GUIStyle("box");
			button.normal.background = textureButton;
			button.alignment = TextAnchor.MiddleCenter;
			button.hover.background = textureHoveredButton;
			button.hover.textColor = Color.white;
			button.active.background = textureClickedButton;
			button.active.textColor = Color.white;

			GUILayout.BeginArea(new Rect((Screen.width - 210) / 2, (Screen.height - 100) , 210, 60));
			if(GUILayout.Button("Return to Lobby",button,GUILayout.Width(200),GUILayout.Height(50)))
			{
		
				photonView.RPC("RemoveFromPlayerList", PhotonTargets.Others, PhotonNetwork.player.name);
				Destroy(playerList.transform.gameObject);
				PhotonNetwork.LeaveRoom();

			}
			GUILayout.EndArea();
		}
	}

	void Update() 
	{

		if(photonView.isMine)
		{

			Debug.Log("I'm here in update.");

			if(playerEnemy == null)
			{
				Debug.Log("PlayerEnemy is Null!");
				playerEnemy = playerList.GetPlayer(player.PhotonPlayerEnemy.name);
				playerEnemy.CurrentHand = defaultChoice;
				return;
			}

			if(player.IsReady && playerEnemy != null && playerEnemy.IsReady)
			{
					Debug.Log("Playing");
					this.RoundCountDown();
					this.AnimationToPlay();
					this.ChangeHand();


			}

			else if(player.IsReady == false)
			{
					Debug.Log("3rd condition");
					player.IsReady = true;
					Debug.Log("Sending Is Ready");
					photonView.RPC("SendIsReady", player.PhotonPlayerEnemy,true, player.PlayerName);

			}

			else
			{

				Debug.Log(player.IsReady);
				Debug.Log(playerEnemy.IsReady);



			}
		}

	}

	void OnPhotonSerializeView(PhotonStream stream, PhotonMessageInfo info)
	{

		if (stream.isWriting)
		{
			stream.SendNext(gameObject.name);
		}
		else
		{
			gameObject.name = (string)stream.ReceiveNext();
		}
	
	}

	#endregion

	#region photon methods

	public void OnPhotonPlayerDisconnected(PhotonPlayer player)
	{
		
		Debug.Log("A player has disconnected");

	}

	public void OnLeftRoom()
	{

		Application.LoadLevel(DashboardMenu.SceneNameMenu);
	}

	void OnApplicationQuit()
	{

		Debug.Log("Goodbye Suckers!");

	}

	#endregion


	public void SetPossibleEnemies()
	{
		if(playerList.players.Count >= 4)
		{
			int next = mathify.NextOpponent(player.PlayerIndex, game, playerList.players.Count);
			int players = mathify.GetFactor(game);

			Player p = null;
			for(int i = 0, count = 0; i < players; i++)
			{
				p = playerList.players[next + i];
				if(p.IsDefeated == false)
				{
					if(count == 0)
					{
						player.PossibleEnemyOne = p;
						count++;
					}

					else
					{

						player.PossibleEnemyTwo = p;

					}
				}

			}
			//guiTextPossibleEnemies.text = player.PossibleEnemyOne.PlayerName + " or " + player.PossibleEnemyTwo.PlayerName;
		}


	}

	public void ChangeHandPlayerTwo()
	{

		if(roundTimer >= 4)
		{
			spriteRenderer2.sprite = choices[playerEnemy.CurrentHand]; 
			
		}
		else if(roundTimer <= 1)
		{
			spriteRenderer2.sprite = choices[playerEnemy.CurrentHand];
			
		}

		else
		{
			Debug.Log("This is so weird!");
			spriteRenderer2.sprite = choices[defaultChoice];
		}

	}

	public void AssignPlayerToGameObject()
	{

		int index = playerList.GetPlayerAtIndex(PhotonNetwork.player.name);

		if(index == 0)
		{
			playerOne = "PlayerOne";
			playerTwo = "PlayerTwo";
			guiTextPlayerScore = GameObject.Find("PlayerOneScore").guiText;
			guiTextPlayerTwoScore =  GameObject.Find("PlayerTwoScore").guiText;
		}
		
		else
		{
			
			if(index % 2 != 0)
			{
				playerOne = "PlayerTwo";
				playerTwo = "PlayerOne";
				guiTextPlayerScore = GameObject.Find("PlayerTwoScore").guiText;
				guiTextPlayerTwoScore = GameObject.Find("PlayerOneScore").guiText;
			}
			else
			{
				playerOne = "PlayerOne";
				playerTwo = "PlayerTwo";
				guiTextPlayerScore = GameObject.Find("PlayerOneScore").guiText;
				guiTextPlayerTwoScore =  GameObject.Find("PlayerTwoScore").guiText;
			}
			
		}


	}

	public void ChangeHand()
	{
		if(roundTimer >= 4)
		{
			spriteRenderer.sprite = choices[currentSetBoolAnimator];
			spriteRenderer2.sprite = choices[playerEnemy.CurrentHand];


		}
		else if(roundTimer <= 1)
		{
			spriteRenderer.sprite = choices[currentSetBoolAnimator];
			spriteRenderer2.sprite = choices[playerEnemy.CurrentHand];


		}

	}

	public void DoThisShit()
	{

		photonView.RPC("SendChangeHand", player.PhotonPlayerEnemy, currentSetBoolAnimator, player.PlayerName);
		player.CurrentHand = currentSetBoolAnimator;

	}

	public void AnimationToPlay()
	{
		
		if(roundTimer <= 3)
		{

			animatorTimer -= Time.deltaTime;
			if(isAnimationChosen == false)
			{
				playedAnimation = currentSetBoolAnimator;
				isAnimationChosen = true;
				if(anim.GetBool(setBoolAnimator[playedAnimation]) == false)
				{
					anim.SetBool(setBoolAnimator[playedAnimation],true);
					animEnemy.SetBool(setBoolAnimator[playedAnimation], true);

				}
			}
		}

	}

	public void RoundCountDown()
	{

		roundTimer -= Time.deltaTime;
		
		if(roundTimer <= 0)
		{
			Debug.Log("Timer is less than zero.");
			if(anim.GetBool(setBoolAnimator[playedAnimation]) && animEnemy.GetBool(setBoolAnimator[playedAnimation]))
			{
				anim.SetBool(setBoolAnimator[playedAnimation],false);
				animEnemy.SetBool(setBoolAnimator[playedAnimation], false);
				RockObject.GetComponent<SpriteRenderer>().enabled = false;
				PaperObject.GetComponent<SpriteRenderer>().enabled = false;
				ScissorsObject.GetComponent<SpriteRenderer>().enabled = false;
			}
			this.AfterRound();

		}
		else
		{	
			Debug.Log("Timer is greater than zero.");
			guiTextTimer.text = roundTimer.ToString("0.0").Replace(".",":");
		}


	}

	public void AfterRound()
	{

		roundFinishTimer -= Time.deltaTime;

		if(roundFinishTimer <= 0)
		{
			if(player.WinCount == winLimit)
			{
				if(!playerList.IsUltimateWinner(player.PlayerName))
				{

					if(roundFinishTimer <= -1)
					{
						if(player.IsWaiting == false)
						{
							guiTextAfterRound.text = "Waiting for your next opponent";
							Debug.Log("Waiting status and finding waiting players is called");
							this.GetNewChallenger();
						}

						else 
						{

							
							if(player.PossibleEnemyOne.IsWaiting)
							{
								player.PhotonPlayerEnemy = player.PossibleEnemyOne.PhotonPlayer;
								playerEnemy = player.PossibleEnemyOne;
								this.ResetGameDataForNextChallenge();

							}
							
							else if(player.PossibleEnemyTwo.IsWaiting)
							{
								
								player.PhotonPlayerEnemy = player.PossibleEnemyTwo.PhotonPlayer;
								playerEnemy = player.PossibleEnemyTwo;
								this.ResetGameDataForNextChallenge();
								
							}

						}
					}

					else
						guiTextAfterRound.text = "You Win the Battle!";
				}
				else
				{
					guiTextAfterRound.text = "You Win the Tournament!";
					isOver = true;
				}
			}
			else if(playerEnemy.WinCount == winLimit)
			{	
				guiTextAfterRound.text = "You Lose the Battle. .";
				isOver = true;
				if(player.IsDefeated == false)
				{
					player.IsDefeated = true;
					photonView.RPC("SendIsDefeated", PhotonTargets.Others, true, player.PlayerName);
				}

				//PhotonNetwork.LeaveRoom();
				//Write Return to lobby code here
			}
			else
				this.ResetGameData();
		}

		else if(int.Parse(roundFinishTimer.ToString("F0")) >= 3)
		{

			this.RecordScore();

		}
						
		else if(player.WinCount < winLimit && playerEnemy.WinCount < winLimit)
			guiTextAfterRound.text = "Get Ready!";

	}

	public void ResetGameData()
	{

		RockObject.GetComponent<SpriteRenderer>().enabled = true;
		PaperObject.GetComponent<SpriteRenderer>().enabled = true;
		ScissorsObject.GetComponent<SpriteRenderer>().enabled = true;

		isResultRecorded = false;
		currentSetBoolAnimator = defaultChoice;
		this.ResetTimer(TimerType.Animator);
		this.ResetTimer(TimerType.Round);
		isAnimationChosen = false;
		this.ResetTimer(TimerType.RoundEnded);
		spriteRendererFace.sprite = happyFace;
		spriteRendererFace2.sprite = happyFace;
		player.CurrentHand = defaultChoice;
		playerEnemy.CurrentHand = defaultChoice;
		guiTextAfterRound.text = string.Empty;


	}

	public void ResetGameDataForNextChallenge()
	{

		spriteRendererLifeBar.sprite = fullLife;
		spriteRendererLifeBar2.sprite = fullLife;
		guiTextPlayerTwoScore.text = player.PhotonPlayerEnemy.name;
		player.WinCount = 0;
		player.IsWaiting = false;
		playerEnemy.WinCount = 0;
		playerEnemy.IsWaiting = false;
		playerEnemy.IsReady = true;
		this.ResetGameData();

		if(mathify.GetFactor(game + 1) < playerList.players.Count)
		{
			game++;
			this.SetPossibleEnemies();
		
		}

	}

	public void DeductLife(SpriteRenderer spriteToChange)
	{

		if(spriteToChange.sprite == twoThirdsLife)
			spriteToChange.sprite = oneThirdLife;
		else if(spriteToChange.sprite == oneThirdLife)
			spriteToChange.sprite = zeroLife;
		else
			spriteToChange.sprite = twoThirdsLife;

	}

	public void RecordScore()
	{

		if(isResultRecorded == false)
		{

			isResultRecorded = true;
			
			if((player.CurrentHand + 1) % 3 == playerEnemy.CurrentHand)
			{
				guiTextAfterRound.text = "You Lose!";
				spriteRendererFace.sprite = sadFace;
				spriteRendererFace2.sprite = happyFace;
				this.DeductLife(spriteRendererLifeBar);

			}
			else if(player.CurrentHand == playerEnemy.CurrentHand)
				guiTextAfterRound.text = "It's a Tie!";
			else
			{
				player.WinCount += 1;
				photonView.RPC("SendWinCount",player.PhotonPlayerEnemy, 1, player.PlayerName);
				guiTextAfterRound.text = "You Win!";
				spriteRendererFace.sprite = happyFace;
				spriteRendererFace2.sprite = sadFace;
				this.DeductLife(spriteRendererLifeBar2);
			}
		}

	}

	public void GetNewChallenger()
	{

			SendIsWaiting(true, player.PlayerName);
			photonView.RPC("SendIsWaiting", PhotonTargets.Others , true,player.PlayerName);

		
	}

	public void ResetTimer(TimerType timerType)
	{
		if(timerType == TimerType.Animator)
			animatorTimer = animatorTime;
		else if(timerType == TimerType.Round)
			roundTimer = roundTime;
		else if(timerType == TimerType.RoundEnded)
			roundFinishTimer = roundFinishTime;
	}

	[RPC]
	public void SendIsWaiting(bool isWaiting, string playerName)
	{
		
		playerList.GetPlayer(playerName).IsWaiting = isWaiting;

	}


	[RPC]
	public void SendIsDefeated(bool isDefeated, string playerName)
	{

		playerList.GetPlayer(playerName).IsDefeated = isDefeated;

	}

	[RPC]
	public void SendWinCount(int count, string playerName)
	{
		playerList.GetPlayer(playerName).WinCount += count;

	}

	[RPC]
	public void SendAnimation(string parameter, bool trueOrFalse)
	{

		animEnemy.SetBool(parameter, trueOrFalse);

	}

	[RPC]
	public void SendChangeHand(int index, string playerName)
	{
		playerList.GetPlayer(playerName).CurrentHand = index;
	}

	[RPC]
	public void SendIsReady(bool isReady, string playerName)
	{
		playerList.GetPlayer(playerName).IsReady = true;

	}



	public enum TimerType{ Round, Animator, RoundEnded }
}
