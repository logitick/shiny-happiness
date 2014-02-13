 using UnityEngine;
using System.Collections;

public class playerController : MonoBehaviour {
	private float startTime;
	private float restSeconds;
	private int roundedRestSeconds;
	private float displaySeconds;
	private float displayMinutes;
	public int CountDownSeconds=10;
	private float Timeleft;
	public string timetext;
	public int p1Wins;
	public int p2Wins;
	private int round;
	private bool isRock = false;
	private bool isPaper = false;
	private bool isScissors = false;
	private string rockS = "null";
	private string paperS = "null";
	private string scissorsS = "null";
	private string winner = "null";
	private string[]rps;
	public string p1Choice = null;
	public string p2Choice = "null";
	public bool playersTurn = true;
	public bool gOver = false;
	public Sprite rockSprite;
	public Sprite paperSprite;
	public Sprite scissorsSprite;
	public string p2rps;
	public string p1rps;
	public string WINNER = null;
	public string p2c;
	public string screenText;
	public string screenText2;
	public int p1Life;
	public int p2Life;
	public player2Controller player2;
	public bool startGame;
	public string player1Name;




	// Use this for initialization
	void Start () {

		rps = new string[] {"rock","paper","scissors"};
		round = 1;
		p1Wins = 0;
		p2Wins = 0;
		startTime=Time.deltaTime;

		try
		{
		player1Name = GameObject.Find("PlayerName").GetComponent<PlayerNameContainer>().playerName;
		}
		catch
		{

			player1Name = "Paul";

		}
	}
	
	void Awake(){
		startGame = false;
		p1Life = 0;
		p2Life = 0;
		GameObject rock = GameObject.Find("rock");
		isRock = rock.GetComponent<rpsClickIndicator>().isSelected;
		rockS = rock.GetComponent<rpsClickIndicator>().objName;
		GameObject paper = GameObject.Find("paper");
		isPaper = paper.GetComponent<rpsClickIndicator>().isSelected;
		paperS = paper.GetComponent<rpsClickIndicator>().objName;
		
		GameObject scissors = GameObject.Find("scissors");
		isScissors = scissors.GetComponent<rpsClickIndicator>().isSelected;
		scissorsS = scissors.GetComponent<rpsClickIndicator>().objName;

		player2 = GameObject.Find("Players Object").transform.GetChild(1).transform.GetComponent<player2Controller>();
	}

	//UPDATE FUNCTION
	void Update()
	{
		if(Input.GetMouseButtonDown(0)){
			RaycastHit hitInfo = new RaycastHit();
			bool hit2 = Physics.Raycast(Camera.main.ScreenPointToRay(Input.mousePosition), out hitInfo);


		}

		if(startGame)
		{
				p1rps = "null";
				WINNER = "NONE";

				Debug.Log(screenText);
				//p2rps = "scissors";
				screenText2 = p2rps.ToUpper();

				p1rps = Choice();
				playersTurn = false;
				int inty = 0;
				if(timetext.Length > 0 && int.TryParse(timetext[2].ToString(), out inty) && int.Parse(timetext[2].ToString()) > 2){
						p2rps = player2.p2Choice();
				}
				if(gameOver(p1Wins, p2Wins) != true){ 					   //false if restart

					if(playersTurn == false && timetext == "000"){	      //false when restart

						if(p1rps == null){
							//p1rps = randomChoice();
							p1rps = "rock";
						 	screenText = p1rps.ToUpper();
							Debug.Log("RANDOM");
						}
						Debug.Log("PLAYER 1 picked: "+p1rps);
						Debug.Log("PLAYER 2 picked: "+p2rps);
						
						WINNER = isWinner(p1rps, p2rps);
						
						Debug.Log(WINNER+"====");
						Debug.Log("Player 1 Score: "+p1Wins+"_______"+"Player 2 Score: "+p2Wins);
						Debug.Log("\n\n\n\n\n\n\n\n\n\n\n\n\n");
						ResetTimer();
					}
					//p1rps = "null";
					playersTurn = true; //stops Choice() from running
				}
				else{
					int whoWon = 1;
					
					if(p2Wins > p1Wins){
						whoWon = 2;
					}
					Debug.Log("=======||Player"+whoWon+"||=======");
					Debug.Log("!!!!!!!!!!!!!!!!!!GAME OVER!!!!!!!!!!!!!!!!!!");
				}
		}
	}	
	
	//PLAYER 1 CHOICE
	public string Choice(){
		if(Input.GetMouseButtonDown(0)){
			RaycastHit hitInfo = new RaycastHit();
			bool hit = Physics.Raycast(Camera.main.ScreenPointToRay(Input.mousePosition), out hitInfo);
			
			if (hit){
				if (hitInfo.transform.gameObject.tag == "paper")
				{
					Debug.Log ("PAPER");
					isPaper = true;
					p1Choice = paperS;
					playersTurn = false;
					screenText = "PAPER";
				}
				else if (hitInfo.transform.gameObject.tag == "rock")
				{
					Debug.Log ("ROCK");
					isRock = true;
					p1Choice = rockS;
					playersTurn = false;
					screenText = "ROCK";
				}
				else if (hitInfo.transform.gameObject.tag == "scissors")
				{
					Debug.Log ("SCISSORS");
					isScissors = true;
					p1Choice = scissorsS;
					playersTurn = false;
					screenText = "SCISSORS";
				}
				else{
					Debug.Log("No Hit"); 
					p1Choice = null;
				}
			}	
			else{
				screenText = p1Choice.ToUpper();
			}
		}
		return p1Choice;
	}
	
	//PLAYER 2 CHOICE
	public string randomChoice(){
		int choice = Random.Range(0,3);
		p2Choice = rps[choice];
		screenText = p1Choice.ToUpper();
		return p2Choice;
	}

	//CHECK WHO WON THE ROUND
	string isWinner(string x1, string x2){
		if(x1.Equals(x2)){
			winner = "====TIE AT";
			//screenText = "TIE";
		}
		else if(x1.Equals("rock") && x2.Equals("scissors")){
			winner = "====Player 1 WINS";
			p1Wins++;
			p2Life++;
		}
		else if(x1.Equals("rock") && x2.Equals("paper")){
			winner = "====Player 2 WINS";
			p2Wins++;
			p1Life++;
		}
		else if(x1.Equals("paper") && x2.Equals("rock")){
			winner = "====Player 1 WINS";
			p1Wins++;
			p2Life++;
		}	
		else if(x1.Equals("paper") && x2.Equals("scissors")){
			winner = "====Player 2 WINS";
			p2Wins++;
			p1Life++;
		}
		else if(x1.Equals("scissors") && x2.Equals("rock")){
			winner = "====Player 2 WINS";
			p2Wins++;
			p1Life++;
		}
		else if(x1.Equals("scissors") && x2.Equals("paper")){
			winner = "====Player 1 WINS";
			p1Wins++;
			p2Life++;
		}
		else{
			Debug.Log("G==========================================G");
		}
		return winner;
	}
	
	//CHECK IF GAME IS OVER (ROUND == 3)
	public bool gameOver(int p1, int p2){
		
		if(p1 >= p2)
			round = p1;
		else if(p2 >= p1)
			round = p2;
		
		if(round == 3){
			gOver = true;
		}
		return gOver;
	}

	//TEXT GUI FOR TIME
	void OnGUI()
	{
		if(startGame)
		{
			if(p1Life < 3 && p2Life < 3){
				Timeleft= Time.time-startTime;
				restSeconds = CountDownSeconds-(Timeleft);
			
				roundedRestSeconds=Mathf.CeilToInt(restSeconds);
				displaySeconds = roundedRestSeconds % 11;
				//displayMinutes = (roundedRestSeconds / 60)%60;s
			
				timetext = (displayMinutes.ToString());
				if (displaySeconds >= 10)
				{
					timetext = timetext + displaySeconds.ToString();
				}
				else 
				{
					timetext = timetext + "0" + displaySeconds.ToString();
				}
		    	//GUI.Label(new Rect(400, 0.0f, 100.0f, 75.0f), timetext);
			}
		}
		else
		{

			if(GUILayout.Button("Start Game", GUILayout.Width(100)))
			{

				ResetTimer();
				startGame = true;

			}

		}
	} 

	//Reset the countdown timer back to 00:5
	void ResetTimer()
	{
		startTime = Time.time;
	}
}
