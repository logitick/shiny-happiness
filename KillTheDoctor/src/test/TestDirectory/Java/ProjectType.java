package com.logitick.killthedoctor;

public enum ProjectType {
  DEFAULT(00),
  PHP(01),
  JAVA(02),
  C_SHARP(04);

  private int value;

  ProjectType(int i) {
    this.value = i;
  }
}