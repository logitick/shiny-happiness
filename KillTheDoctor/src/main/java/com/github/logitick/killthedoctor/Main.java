package com.github.logitick.killthedoctor;
import java.awt.*;
import java.io.File;
import java.nio.file.Paths;

public class Main {
    /*
     * KillTheDoctor -path "string to path" [-type java]
     */
    public static void main(String[] args) {
      String path = "";
      String type = "java";

      for (int i = 0; i < args.length; i++) {
        if (args[i].startsWith("-")) {
          if (args[i].equals("-path")) {
            path = args[++i];
          }
          if (args[i].equals("-type")) {
            type = args[++i].toLowerCase();
          }
        }
      }

      try {

        File file = Paths.get(path).toFile();
        System.out.println(file.getAbsolutePath());
        int projectType = ProjectType.DEFAULT;

        if (type.equals("java")) {
          projectType = ProjectType.JAVA;
        }
        if (type.equals("php")) {
          projectType = ProjectType.PHP;
        }
        if (type.equals("csharp")) {
          projectType = ProjectType.C_SHARP;
        }
        ProjectLoader project = ProjectLoader.load(Paths.get(path), new ProjectType(projectType));

        Programmer pr = new Programmer(project);

        pr.start();
      } catch (AWTException e) {
        e.printStackTrace();
      }

    }
}
