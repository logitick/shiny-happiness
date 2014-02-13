using UnityEngine;
using System.Collections;

public class facial2Expression : MonoBehaviour {
	public Sprite loseFace;
	public Sprite winFace;
	// Use this for initialization
	void Start () {
		
	}
	
	// Update is called once per frame
	void Update () {
		changeFace();
	}
	
	void changeFace(){
		string playerText = GameObject.Find("Players Object").transform.GetChild(0).transform.GetComponent<playerController>().WINNER;
		SpriteRenderer spriteRenderer = GetComponent<SpriteRenderer>();
		
		if(playerText == "====Player 1 WINS"){
			spriteRenderer.sprite = loseFace;
		}
		else if(playerText == "====Player 2 WINS"){
			spriteRenderer.sprite = winFace;
		}
		else if(playerText == "====TIE AT"){
			spriteRenderer.sprite = winFace;
		}
	}
}
