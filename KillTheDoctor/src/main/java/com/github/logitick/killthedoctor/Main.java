package com.github.logitick.killthedoctor;

import com.github.logitick.killthedoctor.project.ProjectType;
import com.github.logitick.killthedoctor.project.ProjectTypeFactory;

import java.awt.*;
import java.io.File;
import java.nio.file.Paths;

public class Main {
    /*
     * KillTheDoctor -path "string to path" [-type java]
     */
    public static void main(String[] args) {
      String path = "";
      String strType = "";
      int type = ProjectTypeFactory.JAVA;

      for (int i = 0; i < args.length; i++) {
        if (args[i].startsWith("-")) {
          if (args[i].equals("-path")) {
            path = args[++i];
          }
          if (args[i].equals("-type")) {
            strType = args[++i].toLowerCase();
          }
        }
      }

      try {

        File file = Paths.get(path).toFile();
        System.out.println(file.getAbsolutePath());
        ProjectType projectType = null;

        if (strType.equals("java")) {
          type = ProjectTypeFactory.JAVA;
        }
        if (strType.equals("php")) {
          type = ProjectTypeFactory.PHP;
        }
        if (strType.equals("csharp")) {
          type = ProjectTypeFactory.C_SHARP;
        }

        Project project = Project.load(Paths.get(path), new ProjectTypeFactory().createProjectType(type));

        Programmer pr = new Programmer(project);

        pr.start();
      } catch (AWTException e) {
        e.printStackTrace();
      }

    }
}
