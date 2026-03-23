#version 300 es
// =============================================================================
// ANCHOR:SHADER_FOOD_VERT
// Description: Instanced vertex shader for food chunk circles.
//              Each instance: a_inst_pos (world x,y) + a_inst_size (radius).
//              The quad vertices (a_quad_pos) define a unit square [-1,1]
//              expanded to the food's current size.
// Uniforms:
//   u_worldToNDC - mat3 world→NDC transform: scale by (2/W, 2/H), translate (-1,-1)
// =============================================================================

in vec2  a_quad_pos;     // unit quad vertex [-1,1]
in vec2  a_inst_pos;     // instance: world x, y
in float a_inst_size;    // instance: current radius in world units

uniform mat3 u_worldToNDC;

out vec2  v_local;    // local quad coordinate for circle shaping in frag

void main() {
    v_local = a_quad_pos;

    vec2 world = a_inst_pos + a_quad_pos * a_inst_size;
    vec3 ndc   = u_worldToNDC * vec3(world, 1.0);

    gl_Position = vec4(ndc.xy, 0.0, 1.0);
}
