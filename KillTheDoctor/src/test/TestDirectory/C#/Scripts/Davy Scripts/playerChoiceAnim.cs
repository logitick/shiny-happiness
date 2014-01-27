using UnityEngine;
using System.Collections;

public class playerChoiceAnim : MonoBehaviour {
	
	private Animator anim;
	public string choice;

	void Awake(){
		anim = GetComponent<Animator>();
	}
	// Use this for initialization
	void Start () {
		choice = "end";
	}
	
	// Update is called once per frame
	void Update () {

		animate();
	}

	void animate(){
			string playerText = GameObject.Find("Players Object").transform.GetChild(0).transform.GetComponent<playerController>().screenText;
			string timer = GameObject.Find("Players Object").transform.GetChild(0).transform.GetComponent<playerController>().timetext;
			
		if(playerText == "ROCK"){
				anim.SetBool("toRock",true);
			}
		else if(playerText == "SCISSORS"){
			anim.SetBool("toScissors",true);

			}
		else if(playerText == "PAPER"){
				anim.SetBool("toPaper",true);
			}

		if(timer != "002")
		{		anim.SetBool("toRock",false);
				anim.SetBool("toPaper",false);
				anim.SetBool("toScissors",false);
		}
	}
}
