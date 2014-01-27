package com.github.logitick.killthedoctor;



import java.io.File;
import java.io.FileFilter;
import java.util.ArrayList;

public class ProjectType {
  public static final int DEFAULT = 00;
  public static final int PHP = 01;
  public static final int JAVA = 02;
  public static final int C_SHARP = 03;


  private int value;
  private ArrayList<String> extensions;
  private FileFilter filter;

  public ProjectType(int i) {
    this.value = i;
    this.extensions = this.getProjectFileExtensions();
    this.filter = new ProjectTypeFilter();
  }

  private ArrayList<String> getProjectFileExtensions() {
    ArrayList<String> list = new ArrayList<String>();

    // add default files
    list.add("txt");
    list.add("html");
    list.add("css");
    list.add("js");
    list.add("sql");
    list.add("xml");
    if ((this.value &
        ProjectType.PHP) == ProjectType.PHP) {
      list.add("php");
      list.add("inc");
      list.add("htaccess");
    }
    if ((this.value & ProjectType.JAVA) == ProjectType.JAVA) {
      list.add("java");
      list.add("pom");
    }
    if ((this.value & ProjectType.C_SHARP) == ProjectType.C_SHARP) {
      list.add("cs");
    }

    return list;
  }

  public boolean isValidExtension(String str) {

    for (String ext : this.extensions) {
      if (str.endsWith(ext)) {
        return true;
      }
    }

    return false;
  }

  public FileFilter getFileFilter() {
    return filter;
  }


  private class ProjectTypeFilter implements FileFilter {
    @Override
    public boolean accept(File pathname) {
      if (pathname.isDirectory() || isValidExtension(pathname.getName())) {
        return true;
      }
      return false;
    }
  }
}