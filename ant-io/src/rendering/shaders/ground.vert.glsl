#version 300 es
// =============================================================================
// ANCHOR:SHADER_GROUND_VERT
// Description: Full-screen quad vertex shader for ground pass.
//              No world transform needed — ground fills the entire viewport.
// =============================================================================

in vec2 a_position;   // unit quad: [(-1,-1),(1,-1),(1,1),(-1,1)]
in vec2 a_texcoord;   // [0,1] UVs

out vec2 v_uv;

void main() {
    v_uv = a_texcoord;
    gl_Position = vec4(a_position, 0.0, 1.0);
}
