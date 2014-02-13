using UnityEngine;
using System.Collections;

public class p2Lifebar : MonoBehaviour {
	public Sprite halfLife;
	public Sprite emptyLife;
	public Sprite noLife;
	public SpriteRenderer spriteRenderer;
	// Use this for initialization
	void Start () {
	
	}
	
	// Update is called once per frame
	void Update () {
		changeLife();
	}

	void changeLife(){
		int p2 = GameObject.Find("Players Object").transform.GetChild(0).transform.GetComponent<playerController>().p2Life;
		spriteRenderer = GetComponent<SpriteRenderer>();
		spriteRenderer = GetComponent<SpriteRenderer>();
		string timer = GameObject.Find("Players Object").transform.GetChild(0).transform.GetComponent<playerController>().timetext;
		
		//if(int.Parse(timer) > 5){
		if(p2 == 1){
			spriteRenderer.sprite = halfLife;
		}
		else if(p2 == 2){
			spriteRenderer.sprite = emptyLife;
		}
		else if(p2 == 3){
			spriteRenderer.sprite = noLife;
		}
	}
}
