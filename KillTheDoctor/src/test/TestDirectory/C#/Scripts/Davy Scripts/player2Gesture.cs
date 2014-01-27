using UnityEngine;
using System.Collections;

public class player2Gesture : MonoBehaviour {
	
	public Sprite rockSprite;
	public Sprite paperSprite;
	public Sprite scissorsSprite;
	public SpriteRenderer spriteRenderer;
	public bool timeOver;
	//public SpriteRenderer sprite;
	// Use this for initialization
	void Start () {
		
	}
	
	// Update is called once per frame
	void Update () {
		changeGesture();
	}
	
	void changeGesture(){
		string playerText = GameObject.Find("Players Object").transform.GetChild(0).transform.GetComponent<playerController>().screenText2;
		spriteRenderer = GetComponent<SpriteRenderer>();
		string timer = GameObject.Find("Players Object").transform.GetChild(0).transform.GetComponent<playerController>().timetext;
		
		if(int.Parse(timer) > 5){
			if(playerText == "ROCK"){
				spriteRenderer.sprite = rockSprite;
			}
			else if(playerText == "SCISSORS"){
				spriteRenderer.sprite = scissorsSprite;
			}
			else if(playerText == "PAPER"){
				spriteRenderer.sprite = paperSprite;
			}
		}
	}
	
}
