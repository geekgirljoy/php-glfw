<?php 

glfwInit();

echo glfwGetVersionString() . PHP_EOL;

// configure the window
glfwWindowHint(GLFW_RESIZABLE, GL_TRUE);

// // offscreen
// glfwWindowHint(GLFW_VISIBLE, GLFW_FALSE);

glfwWindowHint(GLFW_CONTEXT_VERSION_MAJOR, 3);
glfwWindowHint(GLFW_CONTEXT_VERSION_MINOR, 3);
glfwWindowHint(GLFW_OPENGL_PROFILE, GLFW_OPENGL_CORE_PROFILE);

// fix for macOs
glfwWindowHint(GLFW_OPENGL_FORWARD_COMPAT, GL_TRUE);

// create the window
if (!$window = glfwCreateWindow(1000, 1000, "PHP GLFW Demo")) {
	die('Could not create window.');
}

// setup the window
glfwMakeContextCurrent($window);
glfwSwapInterval(1);

// setup the shaders

// Vertext shader
$vertexShader = glCreateShader(GL_VERTEX_SHADER);
glShaderSource($vertexShader, 1, "
#version 330 core
layout (location = 0) in vec3 aPos;
out vec3 ourColor;
void main()
{
   gl_Position = vec4(aPos.x, aPos.y, aPos.z, 1.0f);
   ourColor = aPos;
}
");
glCompileShader($vertexShader);
glGetShaderiv($vertexShader, GL_COMPILE_STATUS, $success);

if (!$success) {
	die("Vertex shader could not be compiled.");
}

// fragment shaders
$fragShader = glCreateShader(GL_FRAGMENT_SHADER);
glShaderSource($fragShader, 1, "
#version 330 core
out vec4 FragColor;
in vec3 ourColor;

uniform float time;

void main()
{
   FragColor = vec4(ourColor.x + sin(time / 10), ourColor.y + sin(time / 15), ourColor.z + cos(time / 5), 1.0f);
} 
");
glCompileShader($fragShader);
glGetShaderiv($fragShader, GL_COMPILE_STATUS, $success);

if (!$success) {
	die("Fragment shader could not be compiled.");
}

// link the shaders
$shaderProgram = glCreateProgram();
glAttachShader($shaderProgram, $vertexShader);
glAttachShader($shaderProgram, $fragShader);
glLinkProgram($shaderProgram);

glGetProgramiv($shaderProgram, GL_LINK_STATUS, $linkSuccess);

// if (!$linkSuccess) {
// 	die("Shader program could not be linked.");
// }

// free the shders
glDeleteShader($vertexShader);
glDeleteShader($fragShader);

// Buffers
$VBO; $VAO; 

// verticies
$verticies = [ 
	-0.5, -0.5, 0.0,
     0.5, -0.5, 0.0,
     0.0,  0.5, 0.0
];

glGenVertexArrays(1, $VAO);
glGenBuffers(1, $VBO);

glBindVertexArray($VAO);

glBindBuffer(GL_ARRAY_BUFFER, $VBO);
glBufferDataFloat(GL_ARRAY_BUFFER, $verticies, GL_STATIC_DRAW);

glVertexAttribPointer(0, 3, GL_FLOAT, GL_FALSE, 3, 0);
glEnableVertexAttribArray(0);

// unbind
glBindBuffer(GL_ARRAY_BUFFER, 0); 
glBindVertexArray(0); 

$i = 0;

while (!glfwWindowShouldClose($window))
{
	$i++;

    glClearColor(sin($i / 200), sin($i / 100), sin($i / 500), 1.0);
    //glClearColor(0, 0, 0, 1);
	glClear(GL_COLOR_BUFFER_BIT);

	// shader
	glUseProgram($shaderProgram);

	// set the time
	glUniform1f(glGetUniformLocation($shaderProgram, "time"), glfwGetTime());

	glBindVertexArray($VAO);
	glDrawArrays(GL_TRIANGLES, 0, 3);

	// swap
    glfwSwapBuffers($window);
    glfwPollEvents();
}

glDeleteVertexArrays(1, $VAO);
glDeleteBuffers(1, $VBO);

glfwDestroyWindow($window);

glfwTerminate();