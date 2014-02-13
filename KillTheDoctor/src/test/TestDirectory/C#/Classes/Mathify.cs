using UnityEngine;
using System.Collections;
using System;

namespace Game
{

	public class Mathify 
	{

		public Mathify()
		{



		}
		
		public int GetFactor(int game)
		{
			
			return ((int)Math.Pow(2, game));
			
		}
		
		public int NextOpponent(int index, int game, int players)
		{
			
			int sign = 1;
			int factor = this.GetFactor(game);
			
			if(index % factor > 0)
			{
				index =  (index/factor) * factor;
			}
			int cluster = game * 4;
			
			if(index % cluster > game * 2 - 1)
				sign = -1;
			
			return (index + (players + factor * sign)) % players;
			
		}



	}

}