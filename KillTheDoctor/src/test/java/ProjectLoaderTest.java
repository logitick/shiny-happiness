/**
 * Created by Paul Daniel Iway on 1/25/14.
 */

import org.junit.Before;

import java.io.File;


public class ProjectLoaderTest {

  private File rootDir;
  private final String strPath = "./KillTheDoctor/src/test/TestDirectory";


  @Before
  public void setUp() {
    rootDir = new File("./KillTheDoctor/src/test/TestDirectory");

  }
}
