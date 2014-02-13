using UnityEngine;
using System.Collections;

namespace Game
{

	public class RPSAudio 
	{

		private AudioClip backButton;
		private AudioClip acceptButton;
		private Vector3 position;

		public RPSAudio()
		{

			backButton = Resources.Load("Audio/RPS_Menu_button_accept") as AudioClip;
			acceptButton = Resources.Load("Audio/RPS_Menu_button_Back") as AudioClip;
			position = new Vector3(0,0,0);

		}

		public void PlayAudio(ButtonToPlay buttonToPlay)
		{
			if(buttonToPlay == ButtonToPlay.Accept)
				AudioSource.PlayClipAtPoint(acceptButton,position);
			else if(buttonToPlay == ButtonToPlay.Back)
				AudioSource.PlayClipAtPoint(backButton,position);
		}

	}

	
	public enum ButtonToPlay{Accept, Back, Dashboard}
}
