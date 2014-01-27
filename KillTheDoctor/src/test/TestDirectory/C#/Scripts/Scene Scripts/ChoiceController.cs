using UnityEngine;
using System.Collections;

public class ChoiceController : MonoBehaviour 
{

	private bool isRPSSpriteClicked;
	public PlayerInBattleController playerInBattleController;
	public string playerName;
	public float roundTimer;

	void Start () 
	{
		isRPSSpriteClicked = false;
		playerInBattleController = GameObject.Find(playerName).GetComponent<PlayerInBattleController>();
	
	}
	

	void Update () 
	{

	}

	void OnMouseDown()
	{
		if(playerInBattleController.roundTimer >= 0)
		{
			if(transform.gameObject.name == "rock")
			{
				playerInBattleController.currentSetBoolAnimator = 0;
				playerInBattleController.DoThisShit();
				isRPSSpriteClicked = true;
			}
			else if(transform.gameObject.name == "paper")
			{
				playerInBattleController.currentSetBoolAnimator = 1;
				playerInBattleController.DoThisShit();
				isRPSSpriteClicked = true;
			}
			else if(transform.gameObject.name == "scissors")
			{
				playerInBattleController.currentSetBoolAnimator = 2;
				playerInBattleController.DoThisShit();
				isRPSSpriteClicked = true;
			}
		}
		if(isRPSSpriteClicked)
		{
			//GameObject.Find(playerName).transform.GetComponent<PlayerInBattleController>().ResetTimer(PlayerInBattleController.TimerType.Animator);
			Debug.Log(transform.gameObject.name);
			isRPSSpriteClicked = false;
		}


	}
}
