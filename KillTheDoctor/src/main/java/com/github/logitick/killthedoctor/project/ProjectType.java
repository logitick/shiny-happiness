package com.github.logitick.killthedoctor.project;

import java.io.File;
import java.io.FileFilter;
import java.util.ArrayList;

/**
 * Created by Paul Daniel Iway on 2/12/14.
 */
public abstract class ProjectType {

  private ArrayList<String> extensions;
  private FileFilter filter;

  public ProjectType() {
    this.extensions = new ArrayList<String>();
    // add default extensions
    addExtension(".txt");
    addExtension(".sql");
    addExtension(".xml");
    this.setProjectExtensions();
  }


  private ArrayList<String> getProjectFileExtensions() {
    return extensions;
  }



  public void addExtension(String extension) {
    this.extensions.add(extension);
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
    if (filter == null) {
      filter = new ProjectTypeFilter();
    }
    return filter;
  }

  private class ProjectTypeFilter implements FileFilter {
    @Override
    public boolean accept(File pathname) {
      return pathname.isDirectory() || isValidExtension(pathname.getName());
    }
  }

  public abstract String getName();
  public abstract void setProjectExtensions();

}
